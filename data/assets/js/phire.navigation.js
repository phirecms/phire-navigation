/**
 * Navigation Module Scripts for Phire CMS 2
 */

phire.changeNavItem = function() {
    var val = jax('#nav_from').val();
    if (val != '----') {
        var valAry = val.split('|');
        var id    = valAry[0];
        var type  = valAry[1];
        var uri   = valAry[2];
        var title = valAry[3];
        jax('#nav_href').val(uri);
        jax('#nav_title').val(title);
        jax('#nav_id').val(id);
        jax('#nav_type').val(type);
    }
};

phire.editNavItem = function(branch, title, href, target) {
    jax('#branch_to_edit').val(branch);
    jax('#nav_edit_title').val(title);
    jax('#nav_edit_href').val(href);
    if (target != '') {
        jax('#nav_edit_target').val(target);
    } else {
        jax('#nav_edit_target').val('----');
    }
    jax('#nav-edit').css('top',(300 + jax().getScrollY()) + 'px');
    jax('#nav-edit').fade(100, {
        tween : 25,
        speed : 250
    });
    return false;
};

jax(document).ready(function(){
    if (jax('#navigations-form')[0] != undefined) {
        jax('#checkall').click(function(){
            if (this.checked) {
                jax('#navigations-form').checkAll(this.value);
            } else {
                jax('#navigations-form').uncheckAll(this.value);
            }
        });
        jax('#navigations-form').submit(function(){
            return jax('#navigations-form').checkValidate('checkbox', true);
        });
    }
    if (jax('#navigation-manage-form')[0] != undefined) {
        jax('body').css('overflow', 'visible');

        var leafInputs  = jax('input.leaf-order');
        var rmNavInputs = jax('input.rm-nav-checkbox');
        for (var i = 0; i < leafInputs.length; i++) {
            jax(leafInputs[i]).attrib('tabindex', i + 1001);
        }
        for (var i = 0; i < rmNavInputs.length; i++) {
            jax(rmNavInputs[i]).attrib('tabindex', i + 2001);
        }
    }
});
