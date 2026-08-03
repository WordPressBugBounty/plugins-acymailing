<?php

namespace AcyMailing\Types;

use AcyMailing\Core\AcymObject;

class FileTreeType extends AcymObject
{
    public function display(array $folders, string $currentFolder, string $nameInput): void
    {
        $tree = [];
        foreach ($folders as $root => $children) {
            $tree = array_merge($tree, $this->searchChildren($children, $root));
        }

        echo '<div id="displaytree" class="cell medium-11"><input type="text" readonly name="currentPath" id="currentPath" value="'.esc_attr($currentFolder).'"></div>';
        echo '<div class="cell" id="treefile" style="display: none;">';
        $this->displayTree($tree, $currentFolder);
        echo '</div>';
        echo '<input type="hidden" name="'.esc_attr($nameInput).'" id="'.esc_attr($nameInput).'" value="'.esc_attr($currentFolder).'">';
    }

    private function searchChildren(array $folders, string $root): array
    {
        $tree = [];
        $tree[$root] = [];

        foreach ($folders as $folder) {
            $folder = trim(str_replace($root, '', $folder), '/\\');
            if (empty($folder)) {
                continue;
            }

            $pathParts = explode('/', $folder);
            $variable = &$tree[$root];
            foreach ($pathParts as $pathPart) {
                if (empty($variable[$pathPart])) {
                    $variable[$pathPart] = [];
                }
                $variable = &$variable[$pathPart];
            }
        }

        return $tree;
    }

    private function displayTree(array $tree, string $pathValue, string $path = ''): void
    {
        if (empty($tree)) {
            return;
        }

        echo '<ul>';
        foreach ($tree as $key => $treeItem) {
            if (empty($path)) {
                $currentPath = $key;
                $title = '/';
            } else {
                $currentPath = rtrim($path, '/').'/'.trim($key, '/').'/';
                $title = $key;
            }

            $extraClass = 'tree-closed';
            $icon = 'acymicon-folder';

            if (strpos($pathValue, $currentPath) !== false) {
                $extraClass = $pathValue == $currentPath ? 'tree-current' : '';
                $icon .= '-open';
            }

            if (empty($treeItem)) {
                $extraClass .= ' tree-empty';
            }

            echo '<li class="tree-child-item '.esc_attr($extraClass).'" data-path="'.esc_attr($currentPath).'">
                    <span class="tree-child-title">
                        <i class="'.esc_attr($icon).'"></i> '.esc_html($title).'
                    </span>';
            $this->displayTree($treeItem, $pathValue, $currentPath);
            echo '</li>';
        }
        echo '</ul>';
    }
}
