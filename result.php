<?php

# MantisBT - a php based bugtracking system
# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.
/**
 * CALLERS
 *	This page is called from:
 *	- account_page.php
 *
 * EXPECTED BEHAVIOUR
 *	- Delete the currently logged in user account
 *	- Logout the current user
 *	- Redirect to the page specified in the logout_redirect_page config option
 *
 * CALLS
 *	This page conditionally redirects upon completion
 *
 * RESTRICTIONS & PERMISSIONS
 *	- User must be authenticated
 *	- allow_account_delete config option must be enabled
 * @todo review form security tokens for this page
 * @todo should page_top1 be before meta redirect?
 *
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2013  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */
/**
 * MantisBT Core API's
 */
require_once 'core.php';
form_security_validate('account_delete');
auth_ensure_user_authenticated();
current_user_ensure_unprotected();
# Only allow users to delete their own accounts if allow_account_delete = ON or
# the user has permission to manage user accounts.
if (OFF == config_get('allow_account_delete') && !access_has_global_level(config_get('manage_user_threshold'))) {
    print_header_redirect('account_page.php');
}
# check that we are not deleting the last administrator account
$t_admin_threshold = config_get_global('admin_site_threshold');
if (current_user_is_administrator() && user_count_level($t_admin_threshold) <= 1) {
    trigger_error(ERROR_USER_CHANGE_LAST_ADMIN, ERROR);
}
helper_ensure_confirmed(lang_get('confirm_delete_msg'), lang_get('delete_account_button'));
form_security_purge('account_delete');
$t_user_id = auth_get_current_user_id();
auth_logout();
user_delete($t_user_id);
html_page_top1();
html_page_top2a();
?>

<br />
<div align="center">
<?php 
echo lang_get('account_removed_msg') . '<br />';
print_bracket_link(config_get('logout_redirect_page'), lang_get('proceed'));
?>
</div>

<?php 
html_page_bottom1a();