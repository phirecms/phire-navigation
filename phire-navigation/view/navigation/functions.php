<?php

function printNavOptions($tree, $depth = 0, $ancestor = null) {
    foreach ($tree as $i => $branch):
        if (($depth == 0) && (null !== $ancestor)):
            $ancestor = $i;
        else:
            if (null === $ancestor):
                $ancestor = $i;
            elseif ($depth == substr_count($ancestor , '_')):
                $ancestor = substr($ancestor, 0, strrpos($ancestor, '_')) . '_' . $i;
            elseif ($depth != substr_count($ancestor , '_')):
                $ancestor .= '_' . $i;
            endif;
        endif; ?>
                    <option value="<?=$ancestor; ?>"><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $depth) . (($depth > 0) ? '&rarr; ' : '') . $branch['name']; ?></option>
<?php
        if (count($branch['children']) > 0):
            printNavOptions($branch['children'], $depth + 1, $ancestor);
        endif;
    endforeach;
}

function printNavTree($tree, $depth = 0, $ancestor = null) {
    foreach ($tree as $i => $branch):
        if (($depth == 0) && (null !== $ancestor)):
            $ancestor = $i;
        else:
            if (null === $ancestor):
                $ancestor = $i;
            elseif ($depth == substr_count($ancestor , '_')):
                $ancestor = substr($ancestor, 0, strrpos($ancestor, '_')) . '_' . $i;
            elseif ($depth != substr_count($ancestor , '_')):
                $ancestor .= '_' . $i;
            endif;
        endif; ?>
            <div class="nav-branch" style="width: <?='calc(' . (100 - (10 * $depth)); ?>% - 22px);">
                <span class="rm-nav-span">Remove?</span><input class="rm-nav-checkbox" type="checkbox" name="rm_nav[]" value="<?=$ancestor; ?>" />
                <a href="<?=$branch['href']; ?>" target="_blank"><?=$branch['name']; ?></a>
            </div>
<?php
        if (count($branch['children']) > 0):
            printNavTree($branch['children'], $depth + 1, $ancestor);
        endif;
    endforeach;
}
