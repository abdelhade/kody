<?php
/**
 * Kody Unified UI Helper Functions (includes/ui_helper.php)
 * Central component generators for pages, tables, cards, and actions.
 */

if (!function_exists('kody_page_header')) {
    /**
     * Renders a unified modern page header.
     *
     * @param string $title Page title
     * @param string $icon FontAwesome icon class (e.g. 'fa-users', 'fa-cogs')
     * @param array $breadcrumbs Key-value pair ['Label' => 'url' or '']
     * @param array $actions Array of buttons [['label' => '', 'url' => '', 'class' => 'k-btn-primary', 'icon' => 'fa-plus']]
     * @param string|int|null $badge Optional counter or status text
     * @return string HTML output
     */
    function kody_page_header(string $title, string $icon = 'fa-folder', array $breadcrumbs = [], array $actions = [], $badge = null): string
    {
        $badgeHtml = '';
        if ($badge !== null && $badge !== '') {
            $badgeHtml = '<span class="k-badge k-badge-primary ml-2">' . htmlspecialchars((string)$badge, ENT_QUOTES, 'UTF-8') . '</span>';
        }

        $crumbsHtml = '';
        if (!empty($breadcrumbs)) {
            $crumbsList = [];
            foreach ($breadcrumbs as $crumbLabel => $crumbUrl) {
                if ($crumbUrl !== '') {
                    $crumbsList[] = '<a href="' . htmlspecialchars($crumbUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($crumbLabel, ENT_QUOTES, 'UTF-8') . '</a>';
                } else {
                    $crumbsList[] = '<span>' . htmlspecialchars($crumbLabel, ENT_QUOTES, 'UTF-8') . '</span>';
                }
            }
            $crumbsHtml = '<ul class="k-breadcrumb">' . implode('<li class="k-breadcrumb-sep">/</li>', array_map(function($c) {
                return '<li>' . $c . '</li>';
            }, $crumbsList)) . '</ul>';
        }

        $actionsHtml = '';
        if (!empty($actions)) {
            $actionsHtml .= '<div class="k-page-actions">';
            foreach ($actions as $action) {
                $btnClass = $action['class'] ?? 'k-btn-primary';
                if (strpos($btnClass, 'k-btn') === false) {
                    $btnClass = 'k-btn ' . $btnClass;
                } else {
                    $btnClass = 'k-btn ' . $btnClass;
                }
                $btnUrl = $action['url'] ?? '#';
                $btnLabel = $action['label'] ?? '';
                $btnIcon = isset($action['icon']) && $action['icon'] !== '' ? '<i class="fas ' . htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8') . '"></i> ' : '';
                $extraAttr = $action['attr'] ?? '';

                $actionsHtml .= '<a href="' . htmlspecialchars($btnUrl, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($btnClass, ENT_QUOTES, 'UTF-8') . '" ' . $extraAttr . '>';
                $actionsHtml .= $btnIcon . htmlspecialchars($btnLabel, ENT_QUOTES, 'UTF-8');
                $actionsHtml .= '</a>';
            }
            $actionsHtml .= '</div>';
        }

        $html = '<div class="k-page-header">';
        $html .= '  <div class="k-page-header-start">';
        $html .= '    <div class="k-page-icon"><i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i></div>';
        $html .= '    <div>';
        $html .= '      <h1 class="k-page-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . $badgeHtml . '</h1>';
        $html .= '      ' . $crumbsHtml;
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '  ' . $actionsHtml;
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('kody_card_start')) {
    /**
     * Starts a unified modern card.
     */
    function kody_card_start(string $title = '', string $icon = '', string $tools = '', bool $flush = false): string
    {
        $html = '<div class="k-card">';
        if ($title !== '' || $tools !== '') {
            $iconHtml = ($icon !== '') ? '<i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . ' text-primary mr-1"></i> ' : '';
            $html .= '<div class="k-card-header">';
            $html .= '  <h3 class="k-card-title">' . $iconHtml . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3>';
            if ($tools !== '') {
                $html .= '  <div class="k-card-tools">' . $tools . '</div>';
            }
            $html .= '</div>';
        }
        $bodyClass = $flush ? 'k-card-body-flush' : 'k-card-body';
        $html .= '<div class="' . $bodyClass . '">';
        return $html;
    }
}

if (!function_exists('kody_card_end')) {
    /**
     * Closes a unified modern card.
     */
    function kody_card_end(): string
    {
        return '</div></div>';
    }
}

if (!function_exists('kody_empty_state')) {
    /**
     * Renders a unified empty state.
     */
    function kody_empty_state(string $title, string $desc = '', string $icon = 'fa-folder-open', ?array $cta = null): string
    {
        $ctaHtml = '';
        if ($cta && !empty($cta['url']) && !empty($cta['label'])) {
            $ctaIcon = isset($cta['icon']) ? '<i class="fas ' . htmlspecialchars($cta['icon'], ENT_QUOTES, 'UTF-8') . ' ml-1"></i> ' : '';
            $ctaHtml = '<a href="' . htmlspecialchars($cta['url'], ENT_QUOTES, 'UTF-8') . '" class="k-btn k-btn-primary">';
            $ctaHtml .= $ctaIcon . htmlspecialchars($cta['label'], ENT_QUOTES, 'UTF-8');
            $ctaHtml .= '</a>';
        }

        $html = '<div class="k-empty-state">';
        $html .= '  <div class="k-empty-icon"><i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i></div>';
        $html .= '  <h4 class="k-empty-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h4>';
        if ($desc !== '') {
            $html .= '  <p class="k-empty-desc">' . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . '</p>';
        }
        $html .= '  ' . $ctaHtml;
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('kody_badge')) {
    /**
     * Renders a unified badge.
     */
    function kody_badge(string $text, string $type = 'info', string $icon = ''): string
    {
        $iconHtml = ($icon !== '') ? '<i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i> ' : '';
        return '<span class="k-badge k-badge-' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '">' . $iconHtml . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}

if (!function_exists('kody_action_btn')) {
    /**
     * Renders standard table row action buttons.
     */
    function kody_action_btn(string $type, string $url, array $options = []): string
    {
        $title = $options['title'] ?? '';
        $itemName = $options['item_name'] ?? '';

        switch ($type) {
            case 'edit':
                $title = $title ?: 'تعديل';
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="k-btn-action k-btn-action-edit" title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-edit"></i></a>';

            case 'view':
                $title = $title ?: 'عرض التفاصيل';
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="k-btn-action k-btn-action-view" title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-eye"></i></a>';

            case 'delete':
                $title = $title ?: 'حذف';
                $attr = 'data-kody-delete="true"';
                if ($itemName !== '') {
                    $attr .= ' data-item-name="' . htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8') . '"';
                }
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="k-btn-action k-btn-action-del" ' . $attr . ' title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-trash-alt"></i></a>';

            default:
                $icon = $options['icon'] ?? 'fa-circle';
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="k-btn-action" title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"><i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i></a>';
        }
    }
}

if (!function_exists('kody_export_excel_button')) {
    /**
     * Renders a unified Excel Export button that automatically integrates with KodyExcel.
     *
     * @param string $tableSelector CSS selector for target table (default: '#myTable')
     * @param string $filename Output file name without extension
     * @param string $title Report title to embed in the spreadsheet header
     * @param array $options Additional options (class, label, icon, company, date, sheet)
     * @return string HTML button
     */
    function kody_export_excel_button(string $tableSelector = '#myTable', string $filename = 'تقرير', string $title = '', array $options = []): string
    {
        $class = $options['class'] ?? 'k-btn k-btn-outline-success';
        if (strpos($class, 'k-btn') === false && strpos($class, 'btn') === false) {
            $class = 'k-btn ' . $class;
        }
        $label = $options['label'] ?? 'تصدير Excel';
        $icon = $options['icon'] ?? 'fa-file-excel';
        $company = $options['company'] ?? '';
        $date = $options['date'] ?? '';
        $sheet = $options['sheet'] ?? '';

        $attrs = ' data-kody-export="true"';
        $attrs .= ' data-table="' . htmlspecialchars($tableSelector, ENT_QUOTES, 'UTF-8') . '"';
        $attrs .= ' data-filename="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '"';
        if ($title !== '') {
            $attrs .= ' data-title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"';
        }
        if ($company !== '') {
            $attrs .= ' data-company="' . htmlspecialchars($company, ENT_QUOTES, 'UTF-8') . '"';
        }
        if ($date !== '') {
            $attrs .= ' data-date="' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '"';
        }
        if ($sheet !== '') {
            $attrs .= ' data-sheet="' . htmlspecialchars($sheet, ENT_QUOTES, 'UTF-8') . '"';
        }

        $iconHtml = ($icon !== '') ? '<i class="fas ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . ' mr-1"></i> ' : '';

        if (!empty($options['dropdown']) && is_array($options['dropdown'])) {
            $splitClass = str_replace('k-btn-block', '', $class);
            $groupClass = $options['group_class'] ?? '';
            $html = '<div class="btn-group ' . htmlspecialchars($groupClass, ENT_QUOTES, 'UTF-8') . '">';
            $html .= '<button type="button" class="' . htmlspecialchars($splitClass, ENT_QUOTES, 'UTF-8') . '"' . $attrs . '>'
                . $iconHtml . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
                . '</button>';
            $html .= '<button type="button" class="' . htmlspecialchars($splitClass, ENT_QUOTES, 'UTF-8') . ' dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="خيارات إضافية للتصدير">';
            $html .= '<span class="sr-only">خيارات التصدير</span>';
            $html .= '</button>';
            $html .= '<div class="dropdown-menu dropdown-menu-right shadow">';
            foreach ($options['dropdown'] as $item) {
                if ($item === 'divider') {
                    $html .= '<div class="dropdown-divider"></div>';
                    continue;
                }
                $itemIcon = !empty($item['icon']) ? '<i class="fas ' . htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') . ' mr-2 ' . htmlspecialchars($item['icon_class'] ?? '', ENT_QUOTES, 'UTF-8') . '"></i> ' : '';
                $itemHref = !empty($item['url']) ? htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') : 'javascript:void(0)';
                $itemTarget = !empty($item['target']) ? ' target="' . htmlspecialchars($item['target'], ENT_QUOTES, 'UTF-8') . '"' : '';
                $itemId = !empty($item['id']) ? ' id="' . htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') . '"' : '';
                $itemAttrs = !empty($item['attrs']) ? ' ' . $item['attrs'] : '';
                $html .= '<a class="dropdown-item" href="' . $itemHref . '"' . $itemId . $itemTarget . $itemAttrs . '>' . $itemIcon . htmlspecialchars($item['label'] ?? '', ENT_QUOTES, 'UTF-8') . '</a>';
            }
            $html .= '</div>';
            $html .= '</div>';
            return $html;
        }

        return '<button type="button" class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' . $attrs . '>'
            . $iconHtml . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</button>';
    }
}

