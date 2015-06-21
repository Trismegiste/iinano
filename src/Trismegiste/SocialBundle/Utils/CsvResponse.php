<?php

/*
 * iinano
 */

namespace Trismegiste\SocialBundle\Utils;

use Iterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * CsvResponse is a http response in CSV format
 */
class CsvResponse extends Response
{

    /** @var Iterator */
    protected $iterator;

    /** @var PropertyPath[] */
    protected $fieldPath = [];

    /** @var \Closure[] */
    protected $fieldRender = [];

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    public function __construct(Iterator $content, array $path)
    {
        parent::__construct('');
        $this->iterator = $content;
        $this->headers->set('Mime-type', 'application/csv');
        $this->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'essai.csv');
        foreach ($path as $label => $column) {
            if (is_string($column)) {
                $this->fieldPath[$label] = new PropertyPath($column);
                $this->fieldRender[$label] = function($val) {
                    return $val;
                };
            } else {
                $this->fieldPath[$label] = new PropertyPath($column['path']);
                $this->fieldRender[$label] = $column['render'];
            }
        }
        $this->propertyAccessor = new PropertyAccessor();
    }

    public function setContent($content)
    {
        $this->iterator = $content;
    }

    public function getContent()
    {
        ob_start();
        $this->sendContent();

        return ob_get_clean();
    }

    public function sendContent()
    {
        // header
        $sepCol = '';
        echo implode(',', array_keys($this->fieldPath));
        // rows
        foreach ($this->iterator as $row) {
            echo PHP_EOL;
            $sepCol = '';
            foreach ($this->fieldPath as $field => $pa) {
                $val = $this->propertyAccessor->getValue($row, $pa);
                $val = call_user_func($this->fieldRender[$field], $val);
                $val = $this->escape($val);
                echo $sepCol, $val;
                $sepCol = ',';
            }
        }
    }

    private function escape($val)
    {
        if (is_null($val)) {
            $val = '';
        } else if (is_string($val)) {
            $val = '"' . str_replace('"', '\"', $val) . '"';
        }

        return $val;
    }

}
