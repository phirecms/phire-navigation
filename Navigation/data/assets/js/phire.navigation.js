/**
 * Navigation Module Scripts for Phire CMS 2
 */

jax(document).ready(function(){
    if (jax('#navigation-form')[0] != undefined) {
        jax('#checkall').click(function(){
            if (this.checked) {
                jax('#navigation-form').checkAll(this.value);
            } else {
                jax('#navigation-form').uncheckAll(this.value);
            }
        });
        jax('#navigation-form').submit(function(){
            return jax('#navigation-form').checkValidate('checkbox', true);
        });
    }
});