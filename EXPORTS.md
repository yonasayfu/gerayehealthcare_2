Exports & Print

Central Trait
- app/Http/Traits/ExportableTrait handles CSV and PDF.
- Controllers/Services call: handleExport, handlePrintCurrent, handlePrintAll, handlePrintSingle.

Configs
- app/Http/Config/ExportConfig.php contains per-module configs (columns, filenames, relations).
- app/Http/Config/AdditionalExportConfigs.php supplements modules like Prescriptions.

Layouts
- resources/views/pdf-layout.blade.php for PDFs; print-layout.blade.php if needed.

Frontend
- useExport composable standardizes export buttons; useTableFilters ensures filter params preserved.
