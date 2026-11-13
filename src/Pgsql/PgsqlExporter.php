<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pgsql;

use Joomla\Database\DatabaseExporter;

/**
 * PDO PostgreSQL Database Exporter.
 *
 * @since  1.5.0
 */
class PgsqlExporter extends DatabaseExporter
{
    /**
     * The database connector to use for exporting structure and/or data.
     *
     * @var    PgsqlDriver
     * @since  1.0
     */
    protected $db;

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
        $buffer[] = '<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $buffer[] = ' <database name="">';

        if ($this->options->withStructure) {
            $buffer = array_merge($buffer, $this->buildXmlStructure());
        }

        if ($this->options->withData) {
            $buffer = array_merge($buffer, $this->buildXmlData());
        }

        $buffer[] = ' </database>';
        $buffer[] = '</postgresqldump>';

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
            $fields    = $this->db->getTableColumns($table, false);
            $keys      = $this->db->getTableKeys($table);
            $sequences = $this->db->getTableSequences($table);

            $buffer[] = '  <table_structure name="' . $table . '">';

            foreach ($sequences as $sequence) {
                $buffer[] = '   <sequence Name="' . $this->escapeXml($this->getGenericTableName($sequence->sequence)) . '"'
                    . ' Schema="' . $this->escapeXml($sequence->schema) . '" Table="' . $this->escapeXml($table) . '"'
                    . ' Column="' . $this->escapeXml($sequence->column) . '" Type="' . $this->escapeXml($sequence->data_type) . '"'
                    . ' Start_Value="' . $this->escapeXml($sequence->start_value) . '" Min_Value="' . $this->escapeXml($sequence->minimum_value) . '"'
                    . ' Max_Value="' . $this->escapeXml($sequence->maximum_value) . '"'
                    . ' Last_Value="' . $this->escapeXml($this->db->getSequenceLastValue($sequence->sequence)) . '"'
                    . ' Increment="' . $this->escapeXml($sequence->increment) . '" Cycle_option="' . $this->escapeXml($sequence->cycle_option) . '"'
                    . ' Is_called="' . $this->escapeXml($this->db->getSequenceIsCalled($sequence->sequence)) . '" />';
            }

            foreach ($fields as $field) {
                $buffer[] = '   <field Field="' . $this->escapeXml($field->column_name) . '" Type="' . $this->escapeXml($field->type) . '"'
                    . ' Null="' . $this->escapeXml($field->null) . '" Default="' . $this->escapeXml($field->Default) . '"'
                    . ' Comments="' . $this->escapeXml($field->comments) . '" />';
            }

            foreach ($keys as $key) {
                $buffer[] = '   <key Index="' . $this->escapeXml($this->getGenericTableName($key->idxName)) . '"'
                    . ' is_primary="' . $this->escapeXml($key->isPrimary) . '" is_unique="' . $this->escapeXml($key->isUnique) . '"'
                    . ' Key_name="' . $this->escapeXml($this->db->getNamesKey($table, $key->indKey)) . '"'
                    . ' Query="' . $this->escapeXml($key->Query) . '" />';
            }

            $buffer[] = '  </table_structure>';
        }

        return $buffer;
    }

    /**
     * Builds the XML data to export.
     *
     * @return  array  An array of XML lines (strings).
     *
     * @since   2.0.0
     * @throws  \Exception if an error occurs.
     */
    protected function buildXmlData()
    {
        $buffer = [];

        foreach ($this->from as $table) {
            // Replace the magic prefix if found.
            $table = $this->getGenericTableName($table);

            // Get the details columns information.
            $fields  = $this->db->getTableColumns($table, false);
            $colblob = [];

            foreach ($fields as $field) {
                // Catch blob for xml conversion
                // PostgreSQL binary large object type
                if ($field->Type == 'bytea') {
                    $colblob[] = $field->Field;
                }
            }

            $query = $this->db->createQuery();
            $query->select($query->quoteName(array_keys($fields)))
                ->from($query->quoteName($table));
            $this->db->setQuery($query);

            $rows = $this->db->loadObjectList();

            if (!count($rows)) {
                continue;
            }

            $buffer[] = '  <table_data name="' . $table . '">';

            foreach ($rows as $row) {
                $buffer[] = '   <row>';

                foreach ($row as $key => $value) {
                    if ($value === null) {
                        $buffer[] = '    <field name="' . $key . '" value_is_null="1"></field>';
                    } elseif (!in_array($key, $colblob)) {
                        $buffer[] = '    <field name="' . $key . '">' . htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8') . '</field>';
                    } else {
                        $buffer[] = '    <field name="' . $key . '" encoding="base64">' . base64_encode($this->db->decodeBinary($value)) . '</field>';
                    }
                }

                $buffer[] = '   </row>';
            }

            $buffer[] = '  </table_data>';
        }

        return $buffer;
    }

    /**
     * Checks if all data and options are in order prior to exporting.
     *
     * @return  $this
     *
     * @since   1.5.0
     * @throws  \RuntimeException
     */
    public function check()
    {
        // Check if the db connector has been set.
        if (!($this->db instanceof PgsqlDriver)) {
            throw new \RuntimeException('Database connection wrong type.');
        }

        // Check if the tables have been specified.
        if (empty($this->from)) {
            throw new \RuntimeException('ERROR: No Tables Specified');
        }

        return $this;
    }
}
