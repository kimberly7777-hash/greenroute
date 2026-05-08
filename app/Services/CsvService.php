<?php

namespace App\Services;

use SplFileObject;
use Exception;

class CsvService
{
    /**
     * Read CSV file and return data as array
     */
    public function readCsv($filePath, $hasHeader = true)
    {
        if (!file_exists($filePath)) {
            throw new Exception("CSV file not found: {$filePath}");
        }

        $data = [];
        $headers = [];
        $row = 0;

        try {
            $file = new SplFileObject($filePath, 'r');
            $file->setFlags(SplFileObject::READ_CSV);

            foreach ($file as $line) {
                if (empty($line[0])) {
                    continue;
                }

                if ($row === 0 && $hasHeader) {
                    $headers = array_map('trim', $line);
                } else {
                    if ($hasHeader && !empty($headers)) {
                        $item = [];
                        foreach ($headers as $index => $header) {
                            $item[$header] = $line[$index] ?? null;
                        }
                        $data[] = array_map('trim', $item);
                    } else {
                        $data[] = array_map('trim', $line);
                    }
                }
                $row++;
            }

            return [
                'headers' => $headers,
                'data' => $data,
                'count' => count($data)
            ];
        } catch (Exception $e) {
            throw new Exception("Error reading CSV file: " . $e->getMessage());
        }
    }

    /**
     * Validate CSV data against rules
     */
    public function validateCsv($data, $rules)
    {
        $errors = [];
        $validData = [];

        foreach ($data as $rowIndex => $row) {
            $rowErrors = [];

            foreach ($rules as $field => $fieldRules) {
                $value = $row[$field] ?? null;

                if (is_string($fieldRules)) {
                    // Simple rule like 'required'
                    if ($fieldRules === 'required' && empty($value)) {
                        $rowErrors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' is required";
                    }
                } elseif (is_array($fieldRules)) {
                    // Complex rules
                    foreach ($fieldRules as $rule) {
                        $this->validateField($value, $rule, $field, $rowIndex, $rowErrors);
                    }
                }
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            } else {
                $validData[] = $row;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $validData,
            'validCount' => count($validData),
            'errorCount' => count($errors)
        ];
    }

    /**
     * Validate individual field
     */
    private function validateField($value, $rule, $field, $rowIndex, &$errors)
    {
        if (strpos($rule, 'required') !== false && empty($value)) {
            $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' is required";
        }

        if (strpos($rule, 'email') !== false && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must be a valid email";
        }

        if (strpos($rule, 'numeric') !== false && !empty($value) && !is_numeric($value)) {
            $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must be numeric";
        }

        if (strpos($rule, 'date') !== false && !empty($value)) {
            if (!strtotime($value)) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must be a valid date";
            }
        }

        if (strpos($rule, 'phone') !== false && !empty($value)) {
            if (!preg_match('/^[0-9\+\-\s\(\)]+$/', $value)) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must be a valid phone number";
            }
        }

        if (strpos($rule, 'min:') !== false && !empty($value)) {
            preg_match('/min:(\d+)/', $rule, $matches);
            if (isset($matches[1]) && strlen($value) < $matches[1]) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must be at least {$matches[1]} characters";
            }
        }

        if (strpos($rule, 'max:') !== false && !empty($value)) {
            preg_match('/max:(\d+)/', $rule, $matches);
            if (isset($matches[1]) && strlen($value) > $matches[1]) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Field '{$field}' must not exceed {$matches[1]} characters";
            }
        }
    }

    /**
     * Export data to CSV
     */
    public function exportToCsv($data, $filename, $headers = null)
    {
        if (empty($data)) {
            throw new Exception("No data to export");
        }

        // Determine headers
        if (is_null($headers)) {
            $headers = array_keys((array)$data[0]);
        }

        // Create temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
        $file = fopen($tempFile, 'w');

        if (!$file) {
            throw new Exception("Could not create temporary CSV file");
        }

        try {
            // Write headers
            fputcsv($file, $headers);

            // Write data
            foreach ($data as $row) {
                $csvRow = [];
                foreach ($headers as $header) {
                    $value = $row[$header] ?? $row->{$header} ?? '';
                    $csvRow[] = $value;
                }
                fputcsv($file, $csvRow);
            }

            fclose($file);

            // Read file content
            $content = file_get_contents($tempFile);
            unlink($tempFile);

            return [
                'success' => true,
                'filename' => $filename,
                'content' => $content,
                'size' => strlen($content),
                'rows' => count($data)
            ];
        } catch (Exception $e) {
            fclose($file);
            unlink($tempFile);
            throw $e;
        }
    }

    /**
     * Process CSV in batches (for large files)
     */
    public function processCsvInBatches($filePath, $batchSize = 100, $callback = null)
    {
        $csvData = $this->readCsv($filePath);
        $data = $csvData['data'];
        $totalRows = count($data);
        $processed = 0;
        $results = [];

        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            $batch = array_slice($data, $i, $batchSize);
            
            if ($callback && is_callable($callback)) {
                $batchResult = $callback($batch, $i / $batchSize + 1);
                $results[] = $batchResult;
                $processed += count($batch);
            }
        }

        return [
            'success' => true,
            'totalRows' => $totalRows,
            'processedRows' => $processed,
            'batches' => count($results),
            'results' => $results
        ];
    }

    /**
     * Sanitize CSV data
     */
    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        if (is_string($data)) {
            return trim(strip_tags($data));
        }

        return $data;
    }

    /**
     * Get CSV sample (first N rows)
     */
    public function getSample($filePath, $sampleSize = 10)
    {
        $csvData = $this->readCsv($filePath);
        $sample = array_slice($csvData['data'], 0, $sampleSize);

        return [
            'headers' => $csvData['headers'],
            'sample' => $sample,
            'totalRows' => $csvData['count'],
            'sampleSize' => count($sample)
        ];
    }
}
