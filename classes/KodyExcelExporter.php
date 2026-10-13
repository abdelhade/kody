<?php
/**
 * Kody High-Performance Native Excel Exporter (classes/KodyExcelExporter.php)
 * Creates valid, styled, RTL XLSX files natively using PHP's ZipArchive.
 * 
 * Zero external library dependencies (no Composer ZipStream issues),
 * high memory efficiency, and full Arabic / RTL support.
 */

class KodyExcelExporter
{
    /**
     * Escape XML text entities
     */
    public static function xmlEscape(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Convert 1-based column number (1, 2, 27) to Excel letter (A, B, AA)
     */
    public static function colLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $mod = ($col - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $col = (int) (($col - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Generate complete XLSX binary data from headers and rows.
     *
     * @param array $headers List of column header titles
     * @param array $rows Array of data rows (each row is an array of column values)
     * @param array $options Configuration options:
     *                      - 'title': Report title string
     *                      - 'company': Company name string
     *                      - 'date': Date or period string
     *                      - 'sheetName': Name of worksheet (default: 'البيانات')
     *                      - 'rtl': bool (default: true)
     *                      - 'widths': array of custom column widths
     * @return string Binary XLSX content
     */
    public static function generate(array $headers, array $rows, array $options = []): string
    {
        $rtl = $options['rtl'] ?? true;
        $sheetName = $options['sheetName'] ?? 'البيانات';
        $title = $options['title'] ?? '';
        $company = $options['company'] ?? '';
        $date = $options['date'] ?? '';
        $customWidths = $options['widths'] ?? [];

        $numCols = max(count($headers), 1);
        $colWidths = array_fill(1, $numCols, 14);

        $sheetDataXml = '';
        $currentRow = 1;

        // 1. Metadata / Header Banner
        if ($company !== '' || $title !== '' || $date !== '') {
            if ($company !== '') {
                $sheetDataXml .= '<row r="' . $currentRow . '">';
                $sheetDataXml .= '<c r="A' . $currentRow . '" t="inlineStr" s="3"><is><t>' . self::xmlEscape($company) . '</t></is></c>';
                $sheetDataXml .= '</row>';
                $currentRow++;
            }
            if ($title !== '') {
                $sheetDataXml .= '<row r="' . $currentRow . '">';
                $sheetDataXml .= '<c r="A' . $currentRow . '" t="inlineStr" s="4"><is><t>' . self::xmlEscape($title) . '</t></is></c>';
                $sheetDataXml .= '</row>';
                $currentRow++;
            }
            if ($date !== '') {
                $sheetDataXml .= '<row r="' . $currentRow . '">';
                $sheetDataXml .= '<c r="A' . $currentRow . '" t="inlineStr" s="5"><is><t>' . self::xmlEscape($date) . '</t></is></c>';
                $sheetDataXml .= '</row>';
                $currentRow++;
            }
            // Empty gap row
            $sheetDataXml .= '<row r="' . $currentRow . '"></row>';
            $currentRow++;
        }

        // 2. Column Headers
        if (!empty($headers)) {
            $sheetDataXml .= '<row r="' . $currentRow . '" customHeight="1" ht="26">';
            $colIdx = 1;
            foreach ($headers as $h) {
                $letter = self::colLetter($colIdx);
                $hStr = (string)$h;
                $sheetDataXml .= '<c r="' . $letter . $currentRow . '" t="inlineStr" s="1"><is><t>' . self::xmlEscape($hStr) . '</t></is></c>';

                // Compute column width estimate
                $len = mb_strlen($hStr, 'UTF-8');
                if ($len * 1.4 > $colWidths[$colIdx]) {
                    $colWidths[$colIdx] = (int)ceil($len * 1.4) + 4;
                }
                $colIdx++;
            }
            $sheetDataXml .= '</row>';
            $currentRow++;
        }

        // 3. Data Rows
        $rowIndex = 0;
        foreach ($rows as $row) {
            $styleId = ($rowIndex % 2 === 0) ? '2' : '6'; // Alternating row styles
            $sheetDataXml .= '<row r="' . $currentRow . '" customHeight="1" ht="20">';
            $colIdx = 1;
            foreach ($row as $val) {
                if ($colIdx > $numCols) break;
                $letter = self::colLetter($colIdx);

                if (is_numeric($val) && !preg_match('/^0\d+/', (string)$val)) {
                    $sheetDataXml .= '<c r="' . $letter . $currentRow . '" t="n" s="' . $styleId . '"><v>' . (float)$val . '</v></c>';
                    $valLen = strlen((string)$val);
                } else {
                    $strVal = (string)$val;
                    $sheetDataXml .= '<c r="' . $letter . $currentRow . '" t="inlineStr" s="' . $styleId . '"><is><t>' . self::xmlEscape($strVal) . '</t></is></c>';
                    $valLen = mb_strlen($strVal, 'UTF-8');
                }

                if ($valLen * 1.2 > $colWidths[$colIdx]) {
                    $colWidths[$colIdx] = min(45, (int)ceil($valLen * 1.2) + 3);
                }
                $colIdx++;
            }
            $sheetDataXml .= '</row>';
            $currentRow++;
            $rowIndex++;
        }

        // Apply custom width overrides if provided
        foreach ($customWidths as $cIdx => $w) {
            if (isset($colWidths[$cIdx])) {
                $colWidths[$cIdx] = (int)$w;
            }
        }

        // 4. Construct Columns XML
        $colsXml = '';
        foreach ($colWidths as $cIdx => $w) {
            $colsXml .= '<col min="' . $cIdx . '" max="' . $cIdx . '" width="' . max(12, $w) . '" customWidth="1"/>';
        }

        $lastColLetter = self::colLetter($numCols);
        $totalRowsCount = max($currentRow - 1, 1);
        $dimensionRef = 'A1:' . $lastColLetter . $totalRowsCount;

        // 5. Worksheet XML
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<dimension ref="' . $dimensionRef . '"/>'
            . '<sheetViews><sheetView ' . ($rtl ? 'rightToLeft="1" ' : '') . 'tabSelected="1" workbookViewId="0"/></sheetViews>'
            . '<cols>' . $colsXml . '</cols>'
            . '<sheetData>' . $sheetDataXml . '</sheetData>'
            . '</worksheet>';

        // 6. Styles XML (Pre-styled headers, fonts, borders, fills)
        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="4">'
            . '  <font><sz val="10"/><name val="Segoe UI"/></font>'
            . '  <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Segoe UI"/></font>'
            . '  <font><b/><sz val="14"/><color rgb="FF1E293B"/><name val="Segoe UI"/></font>'
            . '  <font><sz val="9"/><color rgb="FF64748B"/><name val="Segoe UI"/></font>'
            . '</fonts>'
            . '<fills count="5">'
            . '  <fill><patternFill patternType="none"/></fill>'
            . '  <fill><patternFill patternType="gray125"/></fill>'
            . '  <fill><patternFill patternType="solid"><fgColor rgb="FF1E293B"/></patternFill></fill>' // Navy Header
            . '  <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>' // Light alternate row
            . '  <fill><patternFill patternType="solid"><fgColor rgb="FFFFFFFF"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '  <border><left/><right/><top/><bottom/></border>'
            . '  <border>'
            . '    <left style="thin"><color rgb="FFE2E8F0"/></left>'
            . '    <right style="thin"><color rgb="FFE2E8F0"/></right>'
            . '    <top style="thin"><color rgb="FFE2E8F0"/></top>'
            . '    <bottom style="thin"><color rgb="FFE2E8F0"/></bottom>'
            . '  </border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="7">'
            . '  <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' // 0: Default
            . '  <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 1: Header
            . '  <xf numFmtId="0" fontId="0" fillId="4" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' // 2: White Row
            . '  <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 3: Company Title
            . '  <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 4: Report Title
            . '  <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 5: Subtitle / Date
            . '  <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>' // 6: Striped Row
            . '</cellXfs>'
            . '</styleSheet>';

        // 7. Core OpenXML Structural Files
        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::xmlEscape(mb_substr($sheetName, 0, 31, 'UTF-8')) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';

        // 8. Pack into ZipArchive
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_kody_');
        if ($tmp === false) {
            throw new RuntimeException('تعذّر إنشاء الملف المؤقت');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            throw new RuntimeException('تعذّر تهيئة ملف الأرشيف XLSX');
        }

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('xl/workbook.xml', $workbookXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/styles.xml', $stylesXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $binary = file_get_contents($tmp);
        @unlink($tmp);

        if ($binary === false || $binary === '') {
            throw new RuntimeException('تعذّر استخراج محتوى ملف الإكسل');
        }

        return $binary;
    }

    /**
     * Send download headers and output the Excel binary to browser.
     *
     * @param string $filename Download filename (e.g., 'sales_report.xlsx')
     * @param array $headers List of column header titles
     * @param array $rows Array of data rows
     * @param array $options Configuration options
     */
    public static function download(string $filename, array $headers, array $rows, array $options = []): void
    {
        $binary = self::generate($headers, $rows, $options);

        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        // Clean buffer if any output exists
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"; filename*=UTF-8\'\'' . rawurlencode($filename));
        header('Content-Length: ' . strlen($binary));
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        echo $binary;
        exit;
    }

    /**
     * Stream database query directly to Excel download.
     *
     * @param mysqli $conn Active database connection
     * @param string $sql SQL SELECT query
     * @param string $filename Output filename
     * @param array $headerMap Optional associative array ['db_col' => 'عنوان العمود بالعربي']
     * @param array $options Configuration options
     */
    public static function downloadQuery(mysqli $conn, string $sql, string $filename, array $headerMap = [], array $options = []): void
    {
        $res = $conn->query($sql);
        if (!$res) {
            throw new RuntimeException('خطأ في تنفيذ استعلام التصدير: ' . $conn->error);
        }

        $headers = [];
        $rows = [];

        // Auto determine headers if not explicitly mapped
        if (!empty($headerMap)) {
            $headers = array_values($headerMap);
            $keys = array_keys($headerMap);
            while ($row = $res->fetch_assoc()) {
                $r = [];
                foreach ($keys as $k) {
                    $r[] = $row[$k] ?? '';
                }
                $rows[] = $r;
            }
        } else {
            $first = true;
            while ($row = $res->fetch_assoc()) {
                if ($first) {
                    $headers = array_keys($row);
                    $first = false;
                }
                $rows[] = array_values($row);
            }
        }

        self::download($filename, $headers, $rows, $options);
    }
}
