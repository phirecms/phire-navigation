/**
 * Navigation Module Scripts for Phire CMS 2
 */

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
});