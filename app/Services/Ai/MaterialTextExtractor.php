<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;
use ZipArchive;

/**
 * Pulls plain text out of an uploaded course material for Scenario 3. PDFs go
 * through smalot/pdfparser; .docx and .pptx are Office Open XML zips whose text
 * lives in XML entries we read with the built-in ZipArchive. Legacy .doc/.ppt
 * binary formats are not supported and yield an empty string.
 */
class MaterialTextExtractor
{
    /**
     * Extract text from a material stored on the default disk. Returns '' when
     * the format is unsupported or nothing readable could be pulled out.
     */
    public function extract(string $path): string
    {
        $absolute = Storage::path($path);

        if (! is_file($absolute)) {
            return '';
        }

        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'pdf' => $this->fromPdf($absolute),
            'docx' => $this->fromZipEntries($absolute, '#^word/document\.xml$#'),
            'pptx' => $this->fromZipEntries($absolute, '#^ppt/slides/slide\d+\.xml$#'),
            default => '',
        };
    }

    private function fromPdf(string $absolute): string
    {
        try {
            return $this->normalize((new PdfParser)->parseFile($absolute)->getText());
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Concatenate the text of every zip entry whose name matches the pattern.
     */
    private function fromZipEntries(string $absolute, string $pattern): string
    {
        $zip = new ZipArchive;

        if ($zip->open($absolute) !== true) {
            return '';
        }

        $text = '';

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if ($name !== false && preg_match($pattern, $name)) {
                $xml = $zip->getFromName($name);

                if ($xml !== false) {
                    $text .= ' '.$this->xmlToText($xml);
                }
            }
        }

        $zip->close();

        return $this->normalize($text);
    }

    /**
     * Strip Office XML markup to text, keeping word boundaries.
     */
    private function xmlToText(string $xml): string
    {
        $spaced = preg_replace('/</', ' <', $xml) ?? $xml;

        return html_entity_decode(strip_tags($spaced), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Collapse whitespace so the model gets clean, compact text.
     */
    private function normalize(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }
}
