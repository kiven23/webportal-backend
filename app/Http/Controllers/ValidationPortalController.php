<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ValidationPortalController extends Controller
{
    public function checkAuth()
    {
        return response()->json(['message' => 'With authorization']);
    }

    public function validateTemplate(Request $request)
    {
        $request->validate([
            'base_on_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'comparison_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'unique_column_1' => 'required'
        ]);

        $base_file = $request->base_on_file;
        $comparison_file = $request->comparison_file;

        $base_sheet = $this->getActiveSheetFromFile($base_file);
        $comparison_sheet = $this->getActiveSheetFromFile($comparison_file);

        $baseInfo = $this->getHighestRowColumnAndColumnIndex($base_sheet);
        $baseRowCount = $baseInfo['rowCount'];
        $baseHighestColumn = $baseInfo['highestColumn'];
        $baseColumnCount =   $baseInfo['highestColumnIndex'];

        $comparisonInfo = $this->getHighestRowColumnAndColumnIndex($comparison_sheet);
        $comparisonRowCount = $comparisonInfo['rowCount'];
        $comparisonHighestColumn = $comparisonInfo['highestColumn'];
        $comparisonColumnCount =   $comparisonInfo['highestColumnIndex'];

        $comparison_file_name = $comparison_file->getClientOriginalName();
        $base_file_name = $base_file->getClientOriginalName();

        if ($baseColumnCount != $comparisonColumnCount) {
            return response()->json([
                'validationErrorType' => "Column count doesn't match",
                "message" => "<strong>Column count found:</strong> $comparisonColumnCount<br/> <strong>Expected columnn count:</strong> $baseColumnCount",
                "excelSrc" => $comparison_file_name
            ], 500);
        }

        // if ($baseRowCount != $comparisonRowCount && empty($request->unique_column)) {
        //     return response()->json([
        //         'validationErrorType' => "Row count doesn't match",
        //         "message" => "Please enter the unique column for comparison then re-click Validate button",
        //     ], 500);
        // }

        $unique_col_1 = strtolower($request->unique_column_1);
        $unique_col_2 = strtolower($request->unique_column_2);

        $baseHeaders = $base_sheet->rangeToArray(
            "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $base_uniq_col_1_coordinate = "";
        $base_uniq_col_2_coordinate = "";

        foreach ($baseHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            switch ($header_to_lower) {
                case $unique_col_1:
                    $base_uniq_col_1_coordinate = $key;
                    break;
                case $unique_col_2:
                    $base_uniq_col_2_coordinate = $key;
                    break;
            }
        }

        $comparisonHeaders = $comparison_sheet->rangeToArray(
            "A1:" . $comparisonHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $comparison_uniq_col_1_coordinate = "";
        $comparison_uniq_col_2_coordinate = "";
        foreach ($comparisonHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            switch ($header_to_lower) {
                case $unique_col_1:
                    $comparison_uniq_col_1_coordinate = $key;
                    break;
                case $unique_col_2:
                    $comparison_uniq_col_2_coordinate = $key;
                    break;
            }
        }

        $errorData = [
            "errors" => [],
        ];

        $isBaseUniqColOneNotFound = empty($base_uniq_col_1_coordinate);
        $isComparisonUniqColOneNotFound = empty($comparison_uniq_col_1_coordinate);
        if ($isBaseUniqColOneNotFound || $isComparisonUniqColOneNotFound) {
            $errorData["errors"]["unique_column_1"] = "The first unique column entered is not found in " . ($isBaseUniqColOneNotFound && $isComparisonUniqColOneNotFound ? "both files" : ($isBaseUniqColOneNotFound ? $base_file_name : $comparison_file_name));
        }

        if ($unique_col_1 == $unique_col_2) {
            $errorData["errors"]["unique_column_2"] = "The second unique column must not be the same name as the first unique column";
        } else if (!empty($unique_col_2)) {
            $isBaseUniqColTwoNotFound = empty($base_uniq_col_2_coordinate);
            $isComparisonUniqColTwoNotFound = empty($comparison_uniq_col_2_coordinate);

            if ($isBaseUniqColTwoNotFound || $isComparisonUniqColTwoNotFound) {
                $errorData["errors"]["unique_column_2"] = "The second unique column entered is not found in " . ($isBaseUniqColTwoNotFound && $isComparisonUniqColTwoNotFound ? "both files" : ($isBaseUniqColTwoNotFound ? $base_file_name : $comparison_file_name));
            }
        }


        if (count($errorData["errors"]) > 0) {
            return response($errorData, 422);
        }

        $baseUniqColOneArr = $base_sheet->rangeToArray(
            $base_uniq_col_1_coordinate . "2:" . $base_uniq_col_1_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $baseUniqColTwoArr = [];
        if (!empty($base_uniq_col_2_coordinate)) {
            $baseUniqColTwoArr =  $base_sheet->rangeToArray(
                $base_uniq_col_2_coordinate . "2:" . $base_uniq_col_2_coordinate . $baseRowCount,       // The worksheet range that we want to retrieve
                "",        // Value that should be returned for empty cells
                FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE
            );
        }

        $comparisonUniqColOneArr = $comparison_sheet->rangeToArray(
            $comparison_uniq_col_1_coordinate . "2:" . $comparison_uniq_col_1_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $comparisonUniqColTwoArr = [];
        if (!empty($comparison_uniq_col_2_coordinate)) {
            $comparisonUniqColTwoArr =  $comparison_sheet->rangeToArray(
                $comparison_uniq_col_2_coordinate . "2:" . $comparison_uniq_col_2_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
                "",        // Value that should be returned for empty cells
                FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE
            );
        }

        $itemsNotFound = [
            "comparison_sheet_not_found" =>
            [
                "excelSrc" => $base_file_name,
                "notFoundIn" => $comparison_file_name,
                "data" => [],
                "error_count" => 0,
            ],
            "base_sheet_not_found" => [
                "excelSrc" => $comparison_file_name,
                "notFoundIn" => $base_file_name,
                "data" => [],
                "error_count" => 0,
            ]
        ];

        $comparisonNotFoundErrCnt = 0;
        foreach ($baseUniqColOneArr as $key => $baseUniqColOne) {
            $base_uniq_col_1_val = $baseUniqColOne[$base_uniq_col_1_coordinate];
            $base_uniq_col_2_val = count($baseUniqColTwoArr) > 0 ? $baseUniqColTwoArr[$key][$base_uniq_col_2_coordinate] : "";

            $isExisted = false;
            foreach ($comparisonUniqColOneArr as $innerKey => $comparisonUniqColOne) {
                $comparison_uniq_col_1_val =  $comparisonUniqColOne[$comparison_uniq_col_1_coordinate];
                $comparison_uniq_col_2_val = count($comparisonUniqColTwoArr) > 0 ? $comparisonUniqColTwoArr[$innerKey][$comparison_uniq_col_2_coordinate] : "";

                $isUniqOneEqual = ($comparison_uniq_col_1_val == $base_uniq_col_1_val);
                $isUniqTwoEqual = ($comparison_uniq_col_2_val == $base_uniq_col_2_val);

                if ($isUniqOneEqual && $isUniqTwoEqual) {
                    $isExisted = true;
                    break;
                }
            }

            if (!$isExisted) {
                $comparisonNotFoundErrCnt++;
                $itemsNotFound["comparison_sheet_not_found"]["data"][] =  [
                    "no" => $comparisonNotFoundErrCnt,
                    "header" => $baseHeaders[1][$base_uniq_col_1_coordinate],
                    "row" => $key,
                    "value" => $base_uniq_col_1_val,
                ];
            }
        }


        $baseNotFoundErrCnt = 0;
        foreach ($comparisonUniqColOneArr as $key => $comparisonUniqColOne) {
            $comparison_uniq_col_1_val =  $comparisonUniqColOne[$comparison_uniq_col_1_coordinate];
            $comparison_uniq_col_2_val = count($comparisonUniqColTwoArr) > 0 ? $comparisonUniqColTwoArr[$key][$comparison_uniq_col_2_coordinate] : "";

            $isExisted = false;
            foreach ($baseUniqColOneArr as $innerKey => $baseUniqColOne) {
                $base_uniq_col_1_val = $baseUniqColOne[$base_uniq_col_1_coordinate];
                $base_uniq_col_2_val = count($baseUniqColTwoArr) > 0 ? $baseUniqColTwoArr[$innerKey][$base_uniq_col_2_coordinate] : "";

                $isUniqOneEqual = ($comparison_uniq_col_1_val == $base_uniq_col_1_val);
                $isUniqTwoEqual = ($comparison_uniq_col_2_val == $base_uniq_col_2_val);

                if ($isUniqOneEqual && $isUniqTwoEqual) {
                    $isExisted = true;
                    break;
                }
            }

            if (!$isExisted) {
                $baseNotFoundErrCnt++;
                $itemsNotFound["base_sheet_not_found"]["data"][] =  [
                    "no" => $baseNotFoundErrCnt,
                    "header" => $comparisonHeaders[1][$comparison_uniq_col_1_coordinate],
                    "row" => $key,
                    "value" => $comparison_uniq_col_1_val,
                ];
            }
        }

        $itemsNotFound["comparison_sheet_not_found"]["error_count"] = $comparisonNotFoundErrCnt;
        $itemsNotFound["base_sheet_not_found"]["error_count"] = $baseNotFoundErrCnt;

        if ($baseNotFoundErrCnt > 0 || $comparisonNotFoundErrCnt > 0) {
            return response()->json([
                'validationErrorType' => "Data not found",
                "itemsNotFound" => $itemsNotFound,
            ], 500);
        }

        $notEqualColumnsErrCnt = 0;
        $baseArr = [];
        $comparisonArr = [];
        $notEqualColumns = [];
        foreach ($comparisonUniqColOneArr as $key => $comparisonUniqColOne) {
            $comparison_uniq_col_1_val =  $comparisonUniqColOne[$comparison_uniq_col_1_coordinate];
            $comparison_uniq_col_2_val = count($comparisonUniqColTwoArr) > 0 ? $comparisonUniqColTwoArr[$key][$comparison_uniq_col_2_coordinate] : "";

            foreach ($baseUniqColOneArr as $innerKey => $baseUniqColOne) {

                $base_uniq_col_1_val = $baseUniqColOne[$base_uniq_col_1_coordinate];
                $base_uniq_col_2_val = count($baseUniqColTwoArr) > 0 ? $baseUniqColTwoArr[$innerKey][$base_uniq_col_2_coordinate] : "";

                $isUniqOneEqual = ($comparison_uniq_col_1_val == $base_uniq_col_1_val);
                $isUniqTwoEqual = ($comparison_uniq_col_2_val == $base_uniq_col_2_val);

                if ($isUniqOneEqual && $isUniqTwoEqual) {
                    $baseArrKey =  "A$innerKey:" . $baseHighestColumn . $innerKey;
                    if (!array_key_exists($baseArrKey, $baseArr)) {
                        $baseArr[$baseArrKey] = $base_sheet->rangeToArray(
                            "A$innerKey:" . $baseHighestColumn . $innerKey,
                            "",
                            FALSE,
                            FALSE,
                            TRUE,
                        );
                    }
                    $comparisonArrKey = "A$key:" . $comparisonHighestColumn . $key;
                    if (!array_key_exists($comparisonArrKey, $comparisonArr)) {
                        $comparisonArr[$comparisonArrKey] = $comparison_sheet->rangeToArray(
                            "A$key:" . $comparisonHighestColumn . $key,
                            "",
                            FALSE,
                            FALSE,
                            TRUE,
                        );
                    }
                    foreach ($comparisonArr[$comparisonArrKey][$key] as $comparisonKey => $comparison) {
                        foreach ($baseArr[$baseArrKey][$innerKey] as $baseKey => $base) {
                            if ($comparisonKey == $baseKey) {
                                if ($comparison !== $base) {
                                    $notEqualColumnsErrCnt++;
                                    $notEqualColumns[] = [
                                        "no" => $notEqualColumnsErrCnt,
                                        "unique_column" => $comparison_uniq_col_1_val,
                                        "header" => $comparisonHeaders[1][$comparisonKey],
                                        "row" => $key,
                                        "value" => $comparison,
                                        "expected_value" => $base,
                                    ];
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }

        if (count($notEqualColumns) > 0) {
            return response()->json([
                'validationErrorType' => "Cell Values doesn't match. Total number of errors: " . $notEqualColumnsErrCnt,
                "notEqualColumns" => $notEqualColumns,
                "excelSrc" => $comparison_file_name
            ], 500);
        }

        return response()->json(['message' => "Validation successful. No Conflicts found."]);

        // if (!empty($request->unique_column) && $baseRowCount != $comparisonRowCount) {

        //     $baseHeaders = $base_on_sheet->rangeToArray(
        //         "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
        //         "",        // Value that should be returned for empty cells
        //         FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        //         FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        //         TRUE         // Should the array be indexed by cell row and cell column
        //     );

        //     $base_coordinate = "";
        //     $comparison_coordinate = "";
        //     $header_name  = "";

        //     //$uniqueColExistedInBase = false;
        //     foreach ($baseHeaders[1] as $key => $header) {
        //         if (strtolower($header) == strtolower($request->unique_column)) {
        //             $base_coordinate = $key;
        //             $header_name = $header;
        //             //$uniqueColExistedInBase = true;
        //             break;
        //         }
        //     }

        //     $comparisonHeaders = $comparison_sheet->rangeToArray(
        //         "A1:" . $comparisonHighestColumn . "1",     // The worksheet range that we want to retrieve
        //         "",        // Value that should be returned for empty cells
        //         FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        //         FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        //         TRUE         // Should the array be indexed by cell row and cell column
        //     );

        //     //$uniqueColExistedInComparison = false;
        //     foreach ($comparisonHeaders[1] as $key => $header) {
        //         if (strtolower($header) == strtolower($request->unique_column)) {
        //             $comparison_coordinate = $key;
        //             //$uniqueColExistedInComparison = true;
        //             break;
        //         }
        //     }

        //     $isBaseCoordinateNotFound = empty($base_coordinate);
        //     $isComparisonCoordinateNotFound = empty($comparison_coordinate);
        //     if ($isBaseCoordinateNotFound || $isComparisonCoordinateNotFound) {
        //         return response()->json([
        //             "validation_error" => "The unique column entered is not found in " . ($isBaseCoordinateNotFound && $isComparisonCoordinateNotFound ? "both files" : ($isBaseCoordinateNotFound ? $base_file_name : $comparison_file_name)),
        //         ], 500);
        //     }

        //     $itemsNotFound = [
        //         "header_name" => $header_name,
        //         "comparison_sheet_not_found" =>
        //         [
        //             "excelSrc" => $base_file_name,
        //             "data" => [],
        //             "error_count" => 0,
        //         ],
        //         "base_sheet_not_found" => [
        //             "excelSrc" => $comparison_file_name,
        //             "data" => [],
        //             "error_count" => 0,
        //         ]
        //     ];

        //     $baseArray = $base_on_sheet->rangeToArray(
        //         $base_coordinate . "2:" . $base_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
        //         "",        // Value that should be returned for empty cells
        //         FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        //         FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        //         FALSE
        //     );

        //     for ($row = 2; $row <= $comparisonRowCount; ++$row) {
        //         // for ($col = $coordinate; $col != $comparisonHighestColumn; ++$col) {
        //         $value = $comparison_sheet->getCell($comparison_coordinate . $row)
        //             ->getValue();
        //         $isExisted = false;
        //         foreach ($baseArray as $data) {
        //             if ($data[0] == $value) {
        //                 $isExisted = true;
        //                 break;
        //             }
        //         }
        //         if (!$isExisted) {
        //             if (!in_array($value,  $itemsNotFound["comparison_sheet_not_found"]["data"])) {
        //                 $itemsNotFound["comparison_sheet_not_found"]["data"][] = "<strong>Row:</strong> $row<br/> <strong>Value:</strong> $value";
        //             }
        //         }
        //     }

        //     $comparisonArray = $comparison_sheet->rangeToArray(
        //         $comparison_coordinate . "2:" . $comparison_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
        //         "",        // Value that should be returned for empty cells
        //         FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        //         FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        //         FALSE
        //     );

        //     for ($row = 2; $row <= $baseRowCount; ++$row) {
        //         // for ($col = $coordinate; $col != $comparisonHighestColumn; ++$col) {
        //         $value = $base_on_sheet->getCell($base_coordinate . $row)
        //             ->getValue();
        //         $isExisted = false;
        //         foreach ($comparisonArray as $data) {
        //             if ($data[0] == $value) {
        //                 $isExisted = true;
        //                 break;
        //             }
        //         }
        //         if (!$isExisted) {
        //             if (!in_array($value,  $itemsNotFound["base_sheet_not_found"]["data"])) {
        //                 $itemsNotFound["base_sheet_not_found"]["data"][] = "<strong>Row:</strong> $row<br/> <strong>Value:</strong> $value";
        //             }
        //         }
        //     }

        //     $itemsNotFound["comparison_sheet_not_found"]["error_count"] = count($itemsNotFound["comparison_sheet_not_found"]["data"]);
        //     $itemsNotFound["base_sheet_not_found"]["error_count"] = count($itemsNotFound["base_sheet_not_found"]["data"]);

        //     return response()->json([
        //         'validationErrorType' => "Data not found",
        //         "itemsNotFound" => $itemsNotFound,
        //     ], 500);
        // } else {
        //     $headerArray = $base_on_sheet
        //         ->rangeToArray(
        //             "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
        //             "",        // Value that should be returned for empty cells
        //             TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
        //             TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
        //             FALSE         // Should the array be indexed by cell row and cell column
        //         );
        //     $notEqualHeaders = [];
        //     $notEqualColumns = [];
        //     for ($row = 1; $row <= $baseRowCount; ++$row) {
        //         for ($col = 1; $col <= $baseColumnCount; ++$col) {
        //             $base_cell_value = $base_on_sheet->getCellByColumnAndRow($col, $row)->getValue();
        //             $comparison_cell_val = $comparison_sheet->getCellByColumnAndRow($col, $row)->getValue();
        //             if ($row == 1 && (strtolower($comparison_cell_val) != strtolower($base_cell_value))) {
        //                 $notEqualHeaders[] = "<strong>Header Value:</strong> $comparison_cell_val <br/> <strong>Expected Header Value:</strong> $base_cell_value";
        //             } else if ($comparison_cell_val != $base_cell_value) {
        //                 $notEqualColumns[] = [
        //                     "header" => $headerArray[0][$col - 1],
        //                     "row" => $row,
        //                     "value" => $comparison_cell_val,
        //                     "expected_value" => $base_cell_value,
        //                 ];
        //             }
        //         }
        //     }
        // }

        // if (count($notEqualHeaders) > 0) {
        //     return response()->json([
        //         'validationErrorType' => "Headers doesn't match. Total number of errors: " . count($notEqualHeaders),
        //         "notEqualHeaders" => $notEqualHeaders,
        //         "excelSrc" => $comparison_file_name
        //     ], 500);
        // }

        // if (count($notEqualColumns) > 0) {
        //     return response()->json([
        //         'validationErrorType' => "Cell Values doesn't match. Total number of errors: " . count($notEqualColumns),
        //         "notEqualColumns" => $notEqualColumns,
        //         "excelSrc" => $comparison_file_name
        //     ], 500);
        // }

        // return response()->json(['message' => "Validation successful. No Conflicts found."]);
    }

    public function validateGoodReceiptToSerial(Request $request)
    {
        $request->validate([
            'base_on_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'comparison_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'column_to_compare' => 'required',
            'match_column_1' => 'required',
        ]);


        $base_file = $request->base_on_file;
        $comparison_file = $request->comparison_file;

        $base_sheet = $this->getActiveSheetFromFile($base_file);
        $comparison_sheet = $this->getActiveSheetFromFile($comparison_file);

        $baseInfo = $this->getHighestRowColumnAndColumnIndex($base_sheet);
        $baseRowCount = $baseInfo['rowCount'];
        $baseHighestColumn = $baseInfo['highestColumn'];
        $baseColumnCount =   $baseInfo['highestColumnIndex'];

        $comparisonInfo = $this->getHighestRowColumnAndColumnIndex($comparison_sheet);
        $comparisonRowCount = $comparisonInfo['rowCount'];
        $comparisonHighestColumn = $comparisonInfo['highestColumn'];
        $comparisonColumnCount =   $comparisonInfo['highestColumnIndex'];

        $comparison_file_name = $comparison_file->getClientOriginalName();
        $base_file_name = $base_file->getClientOriginalName();

        $column_to_compare = strtolower($request->column_to_compare);
        $alt_col_name = strtolower($request->alt_name_of_col);
        $match_column_1 = strtolower($request->match_column_1);
        $match_column_2 = strtolower($request->match_column_2);

        $baseHeaders = $base_sheet->rangeToArray(
            "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $header_name = "";
        $base_col_to_compare_coordinate = "";
        $base_match_col_1_coordinate = "";
        $base_match_col_2_coordinate = "";
        foreach ($baseHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            switch ($header_to_lower) {
                case $column_to_compare:
                case $alt_col_name:
                    $base_col_to_compare_coordinate = $key;
                    $header_name = $header;
                    break;
                case $match_column_1:
                    $base_match_col_1_coordinate = $key;
                    break;
                case $match_column_2:
                    $base_match_col_2_coordinate = $key;
                    break;
            }
        }

        $comparisonHeaders = $comparison_sheet->rangeToArray(
            "A1:" . $comparisonHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $comparison_col_to_compare_coordinate = "";
        $comparison_match_col_1_coordinate = "";
        $comparison_match_col_2_coordinate = "";
        foreach ($comparisonHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            switch ($header_to_lower) {
                case $column_to_compare:
                case $alt_col_name:
                    $comparison_col_to_compare_coordinate = $key;
                    break;
                case $match_column_1:
                    $comparison_match_col_1_coordinate = $key;
                    break;
                case $match_column_2:
                    $comparison_match_col_2_coordinate = $key;
                    break;
            }
        }

        $errorData = [
            "errors" => [],
        ];

        $isBaseColCompareISEmpty = empty($base_col_to_compare_coordinate);
        $isComparisonColCompareISEmpty = empty($comparison_col_to_compare_coordinate);
        if ($isBaseColCompareISEmpty || $isComparisonColCompareISEmpty) {
            $errorData["errors"]["column_to_compare"] = "Column to compare is not found in " . ($isBaseColCompareISEmpty && $isComparisonColCompareISEmpty ? "both files" : ($isBaseColCompareISEmpty ? $base_file_name : ("$comparison_file_name. If the column name is different from the base file. Please filled up alternate name field")));

            if (!empty($alt_col_name)) {
                if ($column_to_compare == $alt_col_name) {
                    $errorData["errors"]["alt_name_of_col"] = "Alternate column name must not be same as column to compare";
                } else if ($isComparisonColCompareISEmpty) {
                    $errorData["errors"]["alt_name_of_col"] = "Alternate column name not found in $comparison_file_name";
                }
            }
        }

        $isBaseMatchOneIsEmpty = empty($base_match_col_1_coordinate);
        $isComparsionMatchOneIsEmpty = empty($comparison_match_col_1_coordinate);

        if ($isBaseMatchOneIsEmpty || $isComparsionMatchOneIsEmpty) {
            $errorData["errors"]["match_column_1"] = "Match column 1 not found in " . ($isBaseMatchOneIsEmpty && $isComparsionMatchOneIsEmpty ? "both files" : ($isBaseMatchOneIsEmpty ? $base_file_name : $comparison_file_name));
        }

        if (!empty($match_column_2)) {
            $isBaseMatchTwoIsEmpty = empty($base_match_col_2_coordinate);
            $isComparsionMatchTwoIsEmpty = empty($comparison_match_col_2_coordinate);

            if ($match_column_1 == $match_column_2) {
                $errorData["errors"]["match_column_2"] = "Match column 2 name must not be the same as Match column 1 name";
            } else if ($isBaseMatchTwoIsEmpty || $isComparsionMatchTwoIsEmpty) {
                $errorData["errors"]["match_column_2"] = "Match column 2 not found in " . ($isBaseMatchTwoIsEmpty && $isComparsionMatchTwoIsEmpty ? "both files" : ($isBaseMatchTwoIsEmpty ? $base_file_name : $comparison_file_name));
            }
        }

        if (count($errorData["errors"]) > 0) {
            return response()->json($errorData, 422);
        }

        $baseColToCompareArr = $base_sheet->rangeToArray(
            $base_col_to_compare_coordinate . "2:" . $base_col_to_compare_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $baseMatchColOneArr = $base_sheet->rangeToArray(
            $base_match_col_1_coordinate . "2:" . $base_match_col_1_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $baseMatchColTwoArr = [];
        if (!empty($base_match_col_2_coordinate)) {
            $baseMatchColTwoArr =  $base_sheet->rangeToArray(
                $base_match_col_2_coordinate . "2:" . $base_match_col_2_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
                "",        // Value that should be returned for empty cells
                FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE
            );
        }

        $comparisonColToCompareArr = $comparison_sheet->rangeToArray(
            $comparison_col_to_compare_coordinate . "2:" . $comparison_col_to_compare_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $comparisonMatchColOneArr = $comparison_sheet->rangeToArray(
            $comparison_match_col_1_coordinate . "2:" . $comparison_match_col_1_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $comparisonMatchColTwoArr = [];
        if (!empty($comparison_match_col_2_coordinate)) {
            $comparisonMatchColTwoArr =  $comparison_sheet->rangeToArray(
                $comparison_match_col_2_coordinate . "1:" . $comparison_match_col_2_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
                "",        // Value that should be returned for empty cells
                FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                TRUE
            );
        }

        $notMatchDataErrCnt = 0;
        $notMatchData = [];
        foreach ($comparisonColToCompareArr as $key => $comparisonCol) {
            // $base_col_to_compare_value =  baseColToCompareArr
            $comparison_match_col_one_value = $comparisonMatchColOneArr[$key][$comparison_match_col_1_coordinate];
            $comparison_match_col_two_value = count($comparisonMatchColTwoArr) > 0 ? $comparisonMatchColTwoArr[$key][$comparison_match_col_2_coordinate] : "";
            foreach ($baseColToCompareArr as $innerKey => $baseCol) {
                $base_match_col_one_value = $baseMatchColOneArr[$innerKey][$base_match_col_1_coordinate];
                $base_match_col_two_value = count($baseMatchColTwoArr) > 0 ? $baseMatchColTwoArr[$innerKey][$base_match_col_2_coordinate] : "";
                $isMatchOneEqual = ($comparison_match_col_one_value == $base_match_col_one_value);
                $isMatchTwoEqual = ($comparison_match_col_two_value == $base_match_col_two_value);

                if ($isMatchOneEqual && $isMatchTwoEqual) {
                    $comparison_col_to_compare_value = $comparisonCol[$comparison_col_to_compare_coordinate];
                    $base_col_to_compare_value = $baseCol[$base_col_to_compare_coordinate];
                    if ($comparison_col_to_compare_value != $base_col_to_compare_value) {
                        $notMatchDataErrCnt++;
                        $notMatchData[] = [
                            "no" => $notMatchDataErrCnt,
                            "header" => $header_name,
                            "row" => $key,
                            "value" => $comparison_col_to_compare_value,
                            "expected_value" => $base_col_to_compare_value,
                        ];
                    }
                    break;
                }
            }
        }

        if (count($notMatchData) > 0) {
            return response()->json([
                'validationErrorType' => "Data doesn't match. Total number of errors: " . $notMatchDataErrCnt,
                "notMatchData" => $notMatchData,
                "excelSrc" => $comparison_file_name
            ], 500);
        }

        return response()->json(['message' => "Validation successful. No Conflicts found."]);
    }

    public function getHeaders(Request $request)
    {
        $request->validate([
            'base_on_file' => 'bail|file|mimes:csv,txt,ods,xls,xlsx',
            'comparison_file' => 'bail|file|mimes:csv,txt,ods,xls,xlsx',
        ]);


        $base_file = $request->base_on_file;
        $comparison_file = $request->comparison_file;

        $base_sheet = $this->getActiveSheetFromFile($base_file);
        $comparison_sheet = $this->getActiveSheetFromFile($comparison_file);

        $baseInfo = $this->getHighestRowColumnAndColumnIndex($base_sheet);
        $baseRowCount = $baseInfo['rowCount'];
        $baseHighestColumn = $baseInfo['highestColumn'];
        $baseColumnCount =   $baseInfo['highestColumnIndex'];

        $comparisonInfo = $this->getHighestRowColumnAndColumnIndex($comparison_sheet);
        $comparisonRowCount = $comparisonInfo['rowCount'];
        $comparisonHighestColumn = $comparisonInfo['highestColumn'];
        $comparisonColumnCount =   $comparisonInfo['highestColumnIndex'];

        $comparison_file_name = $comparison_file->getClientOriginalName();
        $base_file_name = $base_file->getClientOriginalName();


        $baseHeaders = $base_sheet->rangeToArray(
            "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $comparisonHeaders = $comparison_sheet->rangeToArray(
            "A1:" . $comparisonHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );
    }

    public function validateBPMasterDataCardCodeArInvoice(Request $request)
    {
        $request->validate([
            'base_on_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'comparison_file' => 'bail|required|file|mimes:csv,txt,ods,xls,xlsx',
            'base_match_column' => 'required',
            'comparison_match_column' => 'required',
            'base_columns_to_get' => 'required',
            'comparison_columns_to_get' => 'required'
        ]);


        $base_file = $request->base_on_file;
        $comparison_file = $request->comparison_file;

        $base_sheet = $this->getActiveSheetFromFile($base_file);
        $comparison_sheet = $this->getActiveSheetFromFile($comparison_file);

        $baseInfo = $this->getHighestRowColumnAndColumnIndex($base_sheet);
        $baseRowCount = $baseInfo['rowCount'];
        $baseHighestColumn = $baseInfo['highestColumn'];
        $baseColumnCount =   $baseInfo['highestColumnIndex'];

        $comparisonInfo = $this->getHighestRowColumnAndColumnIndex($comparison_sheet);
        $comparisonRowCount = $comparisonInfo['rowCount'];
        $comparisonHighestColumn = $comparisonInfo['highestColumn'];
        $comparisonColumnCount =   $comparisonInfo['highestColumnIndex'];

        $comparison_file_name = $comparison_file->getClientOriginalName();
        $base_file_name = $base_file->getClientOriginalName();

        $base_match_column = strtolower($request->base_match_column);
        $comparison_match_column = strtolower($request->comparison_match_column);
        $base_columns_to_get = explode(",", $request->base_columns_to_get);
        $comparison_columns_to_get = explode(",", $request->comparison_columns_to_get);

        $baseHeaders = $base_sheet->rangeToArray(
            "A1:" . $baseHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $base_match_col_coordinate = "";
        $base_columns_coordinate = [];
        $base_header_name = "";
        foreach ($baseHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            if ($header_to_lower == $base_match_column) {
                $base_match_col_coordinate = $key;
                $base_header_name = $header;
            }

            foreach ($base_columns_to_get as $column) {
                if (strtolower($column) == $header_to_lower) {
                    $base_columns_coordinate[$column] = $key;
                    break;
                }
            }
        }

        $comparisonHeaders = $comparison_sheet->rangeToArray(
            "A1:" . $comparisonHighestColumn . "1",     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE         // Should the array be indexed by cell row and cell column
        );

        $comparison_match_col_coordinate = "";
        $comparison_columns_coordinate = [];
        $comparison_header_name = "";
        foreach ($comparisonHeaders[1] as $key => $header) {
            $header_to_lower = strtolower($header);
            if ($header_to_lower == $comparison_match_column) {
                $comparison_match_col_coordinate = $key;
                $comparison_header_name = $header;
            }

            foreach ($comparison_columns_to_get as $column) {
                if (strtolower($column) == $header_to_lower) {
                    $comparison_columns_coordinate[$column] = $key;
                    break;
                }
            }
        }

        $errorData = [
            "errors" => [],

        ];

        if (empty($base_match_col_coordinate)) {
            $errorData["errors"]["base_match_column"] = "$base_match_column column is not found in $base_file_name";
        }

        if ($base_match_column == $comparison_match_column) {
            $errorData["errors"]["comparison_match_column"] = "Comparison match column must not the same as base match column";
        } else if (empty($comparison_match_col_coordinate)) {
            $errorData["errors"]["comparison_match_column"] = "$comparison_match_column column is not found in $comparison_file_name";
        }

        $baseColumnNotFound = [];
        foreach ($base_columns_to_get as $column) {
            if (!isset($base_columns_coordinate[$column])) {
                $baseColumnNotFound[] = $column;
            }
        }

        if (count($baseColumnNotFound) > 0) {
            $notFoundColumns = implode(", ", $baseColumnNotFound);
            $errorData["errors"]["base_columns_to_get"] = "$notFoundColumns column/s is/are not found in $base_file_name";
        }

        $comparisonColumnNotFound = [];
        foreach ($comparison_columns_to_get as $column) {
            if (!isset($comparison_columns_coordinate[$column])) {
                $comparisonColumnNotFound[] = $column;
            }
        }

        if (count($comparisonColumnNotFound) > 0) {
            $notFoundColumns = implode(", ", $comparisonColumnNotFound);
            $errorData["errors"]["comparison_columns_to_get"] = "$notFoundColumns column/s is/are not found in $comparison_file_name";
        }

        if (count($errorData["errors"]) > 0) {
            return response($errorData, 422);
        }

        $baseMatchColArr = $base_sheet->rangeToArray(
            $base_match_col_coordinate . "2:" . $base_match_col_coordinate . $baseRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $comparisonMatchColArr = $comparison_sheet->rangeToArray(
            $comparison_match_col_coordinate . "2:" . $comparison_match_col_coordinate . $comparisonRowCount,     // The worksheet range that we want to retrieve
            "",        // Value that should be returned for empty cells
            FALSE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE
        );

        $itemsNotFound = [
            "base_sheet_not_found" => [
                "excelSrc" => $comparison_file_name,
                "notFoundIn" => $base_file_name,
                "data" => [],
                "error_count" => 0,
            ]
        ];

        $baseNotFoundErrCnt = 0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $col = 1;
        foreach ($base_columns_coordinate as $baseHeaderKey => $base_coordinate) {
            $sheet->setCellValueByColumnAndRow($col, $row, $baseHeaderKey);
            $col++;
        }

        foreach ($comparison_columns_coordinate as $comparisonHeaderKey => $comparison_coordinate) {
            $sheet->setCellValueByColumnAndRow($col, $row, $comparisonHeaderKey);
            $col++;
        }
        $row++;
        foreach ($comparisonMatchColArr as $key => $comparisonMatchCol) {
            $comparison_match_val = $comparisonMatchCol[$comparison_match_col_coordinate];
            $isExisted = false;
            foreach ($baseMatchColArr as $innerKey => $baseMatchCol) {
                $base_match_val = $baseMatchCol[$base_match_col_coordinate];
                if ($comparison_match_val == $base_match_val) {
                    $col = 1;
                    foreach ($base_columns_coordinate as $baseHeaderKey => $base_coordinate) {
                        $sheet->setCellValueByColumnAndRow($col, $row, $base_sheet->getCell($base_coordinate . $innerKey)->getValue());
                        $col++;
                    }
                    foreach ($comparison_columns_coordinate as $comparisonHeaderKey => $comparison_coordinate) {
                        $sheet->setCellValueByColumnAndRow($col, $row, $comparison_sheet->getCell($comparison_coordinate . $key)->getValue());
                        $col++;
                    }
                    $isExisted = true;
                    break;
                }
            }
            $row++;
            if (!$isExisted) {
                $baseNotFoundErrCnt++;
                $itemsNotFound["base_sheet_not_found"]["data"][] =  [
                    "no" => $baseNotFoundErrCnt,
                    "header" => $comparisonHeaders[1][$comparison_match_col_coordinate],
                    "row" => $key,
                    "value" => $comparison_match_val,
                ];
            }
        }

        $itemsNotFound["base_sheet_not_found"]["error_count"] = $baseNotFoundErrCnt;

        if ($baseNotFoundErrCnt > 0) {
            return response()->json([
                "validationErrorType" => "Data not found",
                "itemsNotFound" => $itemsNotFound,
            ], 500);
        }

        $headers = array(
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename=BPMasterDataCardCode_ARInvoiceCardCodeResults'  . '_' . date("YmdHis") . '.xlsx'
        );

        return response()->stream(function () use ($spreadsheet) {
            //$writer = new Xlsx($spreadsheet);
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 200,  $headers);
    }

    private function getActiveSheetFromFile($file)
    {
        $file_path = $file->getRealPath();
        $file_type = IOFactory::identify($file_path);
        $file_reader = IOFactory::createReader($file_type);
        if ($file_type == "Csv") {
            $file_encoding = Csv::guessEncoding($file_path);
            $file_reader->setInputEncoding($file_encoding);
        }

        $spread_sheet = $file_reader->load($file_path);
        return $spread_sheet->getActiveSheet();
    }

    private function getHighestRowColumnAndColumnIndex($sheet)
    {
        $rowCount = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn(1);
        $highestColumnIndex =  Coordinate::columnIndexFromString($highestColumn);
        return compact('rowCount', 'highestColumn', 'highestColumnIndex');
    }

    public function exportToExcel(Request $request)
    {
        $excel_headers = $request->excel_headers;
        $data = $request->data;
        $type = $request->type;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        if ($type == "DataNotFound") {
            foreach ($data as $item) {
                $sheet->setCellValueByColumnAndRow(1, $row, "The data listed below are not existed in " . $item["notFoundIn"]);
                $row++;
                $sheet->setCellValueByColumnAndRow(1, $row, "Data From: " . $item["excelSrc"]);
                $row++;
                $this->setSheetHeaders($sheet, $excel_headers, $row);
                $this->setSheetData($sheet, $item["data"], $row);
                $row++;
            }
        } else {
            $this->setSheetHeaders($sheet, $excel_headers, $row);
            $this->setSheetData($sheet, $data, $row);
        }


        $headers = array(
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename=ValidationErrorsResults' . $type . '_' . date("YmdHis") . '.xlsx'
        );

        return response()->stream(function () use ($spreadsheet) {
            //$writer = new Xlsx($spreadsheet);
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 200,  $headers);
    }

    private function setSheetHeaders($sheet, $headers, &$row)
    {
        foreach ($headers as $key => $header) {
            $sheet->setCellValueByColumnAndRow($key + 1, $row, $header);
        }
        $row++;
    }

    private function setSheetData($sheet, $data, &$row, $exclude_header = "no")
    {
        foreach ($data as $key => $errors) {
            $col = 1;
            foreach ($errors as $innerKey => $error) {
                if ($innerKey != $exclude_header) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $error);
                    $col++;
                }
            }
            $row++;
        }
    }
}
