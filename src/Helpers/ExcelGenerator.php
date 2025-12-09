<?php

namespace App\Helpers;

require_once __DIR__ . '/../../libs/xlsxwriter.class.php';

use XLSXWriter;

class ExcelGenerator {

    /**
     * Generates an Excel report for part inventory.
     *
     * @param array $partsData The data for the parts inventory.
     * @param array $sectionsData The data for sections to map IDs to names.
     */
    public function generatePartInventoryReport(array $partsData, array $sectionsData): void {
        $writer = new XLSXWriter();
        $sheet_name = 'Inventario de Partes';

        // Prepare header row
        $header = ['ID', 'Nombre', 'Descripción', 'Tipo Parte', 'Marca Auto', 'Modelo Auto', 'Año Auto', 'Precio', 'Cantidad Disponible', 'Sección'];
        $writer->writeSheetHeader($sheet_name, array_fill_keys($header, 'string')); // Using string type for all for simplicity

        // Map section IDs to names
        $sectionMap = [];
        foreach ($sectionsData as $section) {
            $sectionMap[$section['id']] = $section['nombre_seccion'];
        }

        // Add data rows
        foreach ($partsData as $part) {
            $row = [
                $part['id_parte'],
                $part['nombre_parte'],
                $part['descripcion_parte'],
                $part['tipo_parte'],
                $part['marca_auto'],
                $part['modelo_auto'],
                $part['ano_auto'],
                $part['precio_parte'],
                $part['cantidad_disponible'],
                $sectionMap[$part['id_seccion']] ?? 'Desconocida'
            ];
            $writer->writeSheetRow($sheet_name, $row);
        }

        // Set headers for Excel download
        $filename = 'inventario_partes_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->writeToStdOut();
        exit();
    }
}
