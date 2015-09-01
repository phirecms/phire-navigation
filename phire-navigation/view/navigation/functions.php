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
                <span class="edit-nav-span"><a class="small-link" href="#" onclick="return phire.editNavItem('<?=$ancestor; ?>', '<?=addslashes(htmlentities($branch['name'], ENT_COMPAT, 'UTF-8')); ?>', '<?=$branch['href']; ?>', <?php
if (isset($branch['attributes'])):
    if (isset($branch['attributes']['onclick'])):
        echo "'false'";
    elseif (isset($branch['attributes']['target'])):
        echo "'" . $branch['attributes']['target'] . "'";
    else:
        echo "''";
    endif;
else:
    echo "''";
endif;
?>);">Edit</a></span>
                <a href="<?=$branch['href']; ?>" target="_blank"<?=($branch['href'] == '#') ? ' onclick="return false;"' : null; ?>><?=$branch['name']; ?></a>
            </div>
<?php
        if (count($branch['children']) > 0):
            printNavTree($branch['children'], $depth + 1, $ancestor);
        endif;
    endforeach;
}
