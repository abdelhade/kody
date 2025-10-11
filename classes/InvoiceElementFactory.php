<?php

require_once 'InvoiceElementBase.php';

/**
 * مصنع إنتاج عناصر الفاتورة - تطبيق Factory Pattern مع Polymorphism
 * Invoice Element Factory - implementing Factory Pattern with Polymorphism
 */
class InvoiceElementFactory
{
    /**
     * إنشاء عنصر فاتورة حسب النوع
     * Create invoice element by type
     */
    public static function createElement($elementType, $invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        switch (strtolower($elementType)) {
            case 'header':
                require_once __DIR__ . '/InvoiceHeader.php';
                return new InvoiceHeader($invoiceType, $isEditMode, $data, $conn);
                
            case 'details':
                require_once __DIR__ . '/InvoiceDetails.php';
                return new InvoiceDetails($invoiceType, $isEditMode, $data, $conn);
                
            case 'footer':
                require_once __DIR__ . '/InvoiceFooter.php';
                return new InvoiceFooter($invoiceType, $isEditMode, $data, $conn);
                
            case 'add_item_modal':
                require_once __DIR__ . '/AddItemModal.php';
                return new AddItemModal($invoiceType, $isEditMode, $data, $conn);
                
            default:
                throw new InvalidArgumentException("Unknown element type: $elementType");
        }
    }

    /**
     * إنشاء جميع عناصر الفاتورة
     * Create all invoice elements
     */
    public static function createAllElements($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        return [
            'header' => self::createElement('header', $invoiceType, $isEditMode, $data, $conn),
            'details' => self::createElement('details', $invoiceType, $isEditMode, $data, $conn),
            'footer' => self::createElement('footer', $invoiceType, $isEditMode, $data, $conn),
            'add_item_modal' => self::createElement('add_item_modal', $invoiceType, $isEditMode, $data, $conn)
        ];
    }

    /**
     * التحقق من صحة جميع العناصر
     * Validate all elements
     */
    public static function validateAllElements($elements)
    {
        $allErrors = [];
        
        foreach ($elements as $elementName => $element) {
            if ($element instanceof InvoiceElementBase) {
                $errors = $element->validate();
                if (!empty($errors)) {
                    $allErrors[$elementName] = $errors;
                }
            }
        }
        
        return $allErrors;
    }

    /**
     * عرض جميع العناصر
     * Render all elements
     */
    public static function renderAllElements($elements)
    {
        $output = [];
        
        foreach ($elements as $elementName => $element) {
            if ($element instanceof InvoiceElementBase) {
                $output[$elementName] = $element->render();
            }
        }
        
        return $output;
    }
}