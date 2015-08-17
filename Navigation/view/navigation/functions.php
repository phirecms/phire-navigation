<?php
function printNavTree($tree, $depth = 0, $ancestor = null) {
    foreach ($tree as $i => $branch): ?>
            <div class="nav-branch" style="width: <?='calc(' . (100 - (10 * $depth)); ?>% - 22px);">
                <span class="rm-nav-span">Remove?</span><input class="rm-nav-checkbox" type="checkbox" name="rm_nav[]" value="branch_<?=((null !== $ancestor) ? $ancestor : $i) . '_depth_' . $depth . '_count_' . $i; ?>" />
                <a href="<?=$branch['href']; ?>" target="_blank"><?=$branch['name']; ?></a>
            </div>
<?php
        if (count($branch['children']) > 0):
            printNavTree($branch['children'], $depth + 1, ((null !== $ancestor) ? $ancestor : $i));
        endif;
    endforeach;
}

function printNavOptions($tree, $depth = 0, $ancestor = null) {
    foreach ($tree as $i => $branch): ?>
                    <option value="branch_<?=((null !== $ancestor) ? $ancestor : $i) . '_depth_' . $depth; ?>"><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $depth) . (($depth > 0) ? '&rarr; ' : '') . $branch['name']; ?></option>
<?php
        if (count($branch['children']) > 0):
            printNavOptions($branch['children'], $depth + 1, ((null !== $ancestor) ? $ancestor : $i));
        endif;
    endforeach;
}