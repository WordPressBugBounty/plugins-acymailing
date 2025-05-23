<?php

namespace AcyMailing\Core;

abstract class AcymClass extends AcymObject
{
    protected string $table;

    protected string $pkey = '';

    public array $errors = [];

    public array $messages = [];

    protected bool $forceInsert = false;

    public function getMatchingElements(array $settings = []): array
    {
        if (!empty($this->table) && !empty($this->pkey)) {
            $query = 'SELECT * FROM #__acym_'.acym_secureDBColumn($this->table);
            $queryCount = 'SELECT COUNT(*) AS total FROM #__acym_'.acym_secureDBColumn($this->table);

            if (empty($settings['ordering'])) $settings['ordering'] = $this->pkey;
            $query .= ' ORDER BY `'.acym_secureDBColumn($settings['ordering']).'`';
            if (!empty($settings['ordering_sort_order'])) $query .= ' '.acym_secureDBColumn(strtoupper($settings['ordering_sort_order']));

            $elements = acym_loadObjectList($query);
            $total = acym_loadObject($queryCount);
        } else {
            $elements = [];
            $total = new \stdClass();
            $total->total = 0;
        }

        return [
            'elements' => $elements,
            'total' => $total,
        ];
    }

    public function getOneById($id)
    {
        return acym_loadObject('SELECT * FROM #__acym_'.acym_secureDBColumn($this->table).' WHERE `'.acym_secureDBColumn($this->pkey).'` = '.intval($id));
    }

    public function getByIds($ids)
    {
        if (empty($ids)) return [];

        if (!is_array($ids)) $ids = [$ids];

        acym_arrayToInteger($ids);

        return acym_loadObjectList('SELECT * FROM #__acym_'.acym_secureDBColumn($this->table).' WHERE `'.acym_secureDBColumn($this->pkey).'` IN ("'.implode('","', $ids).'")');
    }

    public function getAll(?string $key = null): array
    {
        if (empty($key)) {
            $key = $this->pkey;
        }

        return acym_loadObjectList('SELECT * FROM #__acym_'.acym_secureDBColumn($this->table), $key);
    }

    public function save($element)
    {
        $tableColumns = acym_getColumns($this->table);
        $cloneElement = clone $element;
        foreach ($cloneElement as $column => $value) {
            if (!in_array($column, $tableColumns)) {
                unset($cloneElement->$column);
                continue;
            }
            acym_secureDBColumn($column);
        }

        $pkey = $this->pkey;

        try {
            if (empty($cloneElement->$pkey) || $this->forceInsert) {
                $status = acym_insertObject('#__acym_'.$this->table, $cloneElement);
            } else {
                $status = acym_updateObject('#__acym_'.$this->table, $cloneElement, $pkey);
            }
        } catch (\Exception $e) {
            $status = false;
        }

        if (!$status) {
            $dbError = strip_tags(isset($e) ? $e->getMessage() : acym_getDBError());
            if (!empty($dbError)) {
                if (strlen($dbError) > 203) $dbError = substr($dbError, 0, 200).'...';
                $this->errors[] = $dbError;
            }

            return false;
        }

        return empty($cloneElement->$pkey) ? $status : $cloneElement->$pkey;
    }

    public function delete($elements)
    {
        if (empty($elements)) {
            return 0;
        }

        if (!is_array($elements)) {
            $elements = [$elements];
        }

        $escapedElements = [];
        foreach ($elements as $key => $val) {
            $escapedElements[$key] = acym_escapeDB($val);
        }

        if (empty($this->pkey) || empty($this->table) || empty($escapedElements)) {
            return false;
        }

        acym_trigger('onAcymBefore'.ucfirst($this->table).'Delete', [&$elements]);

        $query = 'DELETE FROM #__acym_'.acym_secureDBColumn($this->table).' WHERE '.acym_secureDBColumn($this->pkey).' IN ('.implode(',', $escapedElements).')';
        $result = acym_query($query);

        if (!$result) return false;

        acym_trigger('onAcymAfter'.ucfirst($this->table).'Delete', [&$elements]);

        return $result;
    }

    public function setActive($elements)
    {
        if (!is_array($elements)) {
            $elements = [$elements];
        }

        if (empty($elements)) {
            return;
        }

        acym_arrayToInteger($elements);
        acym_query('UPDATE '.acym_secureDBColumn('#__acym_'.$this->table).' SET active = 1 WHERE `'.acym_secureDBColumn($this->pkey).'` IN ('.implode(',', $elements).')');
    }

    public function setInactive($elements)
    {
        if (!is_array($elements)) {
            $elements = [$elements];
        }

        if (empty($elements)) {
            return 0;
        }

        acym_arrayToInteger($elements);
        acym_query('UPDATE '.acym_secureDBColumn('#__acym_'.$this->table).' SET active = 0 WHERE `'.acym_secureDBColumn($this->pkey).'` IN ('.implode(',', $elements).')');
    }
}
