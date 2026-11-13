<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Mysqli;

use Joomla\Database\DatabaseExporter;

/**
 * MySQLi Database Exporter.
 *
 * @since  1.0
 */
class MysqliExporter extends DatabaseExporter
{
    /**
     * Builds the XML data for the tables to export.
     *
     * @return  string  An XML string
     *
     * @since   1.0
     * @throws  \Exception if an error occurs.
     */
    protected function buildXml()
    {
        $buffer = [];

        $buffer[] = '<?xml version="1.0"?>';
        $buffer[] = '<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $buffer[] = ' <database name="">';

        if ($this->options->withStructure) {
            $buffer = array_merge($buffer, $this->buildXmlStructure());
        }

        if ($this->options->withData) {
            $buffer = array_merge($buffer, $this->buildXmlData());
        }

        $buffer[] = ' </database>';
        $buffer[] = '</mysqldump>';

        return implode("\n", $buffer);
    }

    /**
     * Builds the XML structure to export.
     *
     * @return  array  An array of XML lines (strings).
     *
     * @since   1.0
     * @throws  \Exception if an error occurs.
     */
    protected function buildXmlStructure()
    {
        $buffer = [];

        foreach ($this->from as $table) {
            // Replace the magic prefix if found.
            $table = $this->getGenericTableName($table);

            // Get the details columns information.
            $fields = $this->db->getTableColumns($table, false);
            $keys   = $this->db->getTableKeys($table);

            $buffer[] = '  <table_structure name="' . $table . '">';

            foreach ($fields as $field) {
                $buffer[] = '   <field Field="' . $this->escapeXml($field->Field) . '" Type="' . $this->escapeXml($field->Type) . '"'
                    . ' Null="' . $this->escapeXml($field->Null) . '" Key="' . $this->escapeXml($field->Key) . '"'
                    . (isset($field->Default) ? ' Default="' . $this->escapeXml($field->Default) . '"' : '')
                    . ' Extra="' . $this->escapeXml($field->Extra) . '" />';
            }

            foreach ($keys as $key) {
                $buffer[] = '   <key Table="' . $this->escapeXml($table) . '" Non_unique="' . $this->escapeXml($key->Non_unique) . '"'
                    . ' Key_name="' . $this->escapeXml($key->Key_name) . '" Seq_in_index="' . $this->escapeXml($key->Seq_in_index) . '"'
                    . ' Column_name="' . $this->escapeXml($key->Column_name) . '" Collation="' . $this->escapeXml($key->Collation) . '"'
                    . ' Null="' . $this->escapeXml($key->Null) . '" Index_type="' . $this->escapeXml($key->Index_type) . '"'
                    . ' Sub_part="' . $this->escapeXml($key->Sub_part) . '" Comment="' . $this->escapeXml($key->Comment) . '" />';
            }

            $buffer[] = '  </table_structure>';
        }

        return $buffer;
    }

    /**
     * Checks if all data and options are in order prior to exporting.
     *
     * @return  $this
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function check()
    {
        // Check if the db connector has been set.
        if (!($this->db instanceof MysqliDriver)) {
            throw new \RuntimeException('Database connection wrong type.');
        }

        // Check if the tables have been specified.
        if (empty($this->from)) {
            throw new \RuntimeException('ERROR: No Tables Specified');
        }

        return $this;
    }
}
