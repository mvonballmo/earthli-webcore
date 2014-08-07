<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/**
 * @access private
 */
define ('Unassigned', -1);

/**
 * Operating system constant for Unix-based systems.
 * @see Os_win
 * @see ENVIRONMENT::set_os()
 */
define ('Os_unix', 0);

/**
 * Operating system constant for Windows.
 * @see Os_unix
 * @see ENVIRONMENT::set_os()
 */
define ('Os_win', 1);

/**
 * The unique id for an anonymous user.
 */
define ('Anon_user_id', 0);

/**
 * The unique id for the 'global' user.
  * @access private
  */
define ('All_users_id', 0x7FFFFFFF);

/**
 * The unique id for non-existent users.
 * This id is used when a request is made for a user that no longer exists or is invisible.
 */
define ('Non_existent_user_id', 0);

/**
 * Key used to register a custom page renderer.
 */
define ('Custom_page_renderer', 'CUSTOM_PAGE_RENDERER');

/**
 * Permissions for anonymous users.
 */
define ('Privilege_kind_anonymous', 'anonymous');

/**
 * Permissions for registered users.
 */
define ('Privilege_kind_registered', 'registered');

/**
 * Permissions for an individual user.
 */
define ('Privilege_kind_user', 'user');

/**
 * Permissions for a group.
 */
define ('Privilege_kind_group', 'group');

/************************************
 * Names of supported privilege sets
 ************************************/

/**
 * Types of subscriptions available.
 * Constant defined for documentation only
 * @see Privilege_set_global
 * @see Privilege_set_general
 * @see Privilege_set_folder
 * @see Privilege_set_comment
 * @see Privilege_set_entry
 * @see Privilege_set_group
 * @see Privilege_set_user
 * @see Privilege_set_attachment
 * @access private
 */
define ('Privilege_set_constants', '');

/**
 * Permission set for application-level tasks.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_global', 'global_permissions');

/**
 * Permission set for miscellanous tasks.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_general', 'general_permissions');

/**
 * Permission set applied to a {@link FOLDER}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_folder', 'folder_permissions');

/**
 * Permission set applied to a {@link COMMENT}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_comment', 'comment_permissions');

/**
 * Permission set applied to a {@link ENTRY}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_entry', 'entry_permissions');

/**
 * Permission set applied to a {@link GROUP}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_group', 'group_permissions');

/**
 * Permission set applied to a {@link USER}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_user', 'user_permissions');

/**
 * Permission set applied to an {@link ATTACHMENT}.
 * Constant must match name of field in SQL.
 */
define ('Privilege_set_attachment', 'attachment_permissions');

/************************************
 * Privileges for objects
 ************************************/

/**
 * Privileges defined for objects.
 * @see Privilege_view
 * @see Privilege_view_history
 * @see Privilege_view_hidden
 * @see Privilege_create
 * @see Privilege_modify
 * @see Privilege_delete
 * @see Privilege_purge
 * @see Privilege_secure
 * @see Privilege_upload
 */
define ('Privilege_range_object', 'object');

/**
 * Permission to view an object.
 */
define ('Privilege_view', 0x01);

/**
 * Permission to view object histor.
 */
define ('Privilege_view_history', 0x02);

/**
 * Permission to view hidden objects.
 */
define ('Privilege_view_hidden', 0x04);

/**
 * Permission to create an object.
 */
define ('Privilege_create', 0x08);

/**
 * Permission to modify an object.
 */
define ('Privilege_modify', 0x10);

/**
 * Permission to delete an object.
 */
define ('Privilege_delete', 0x20);

/**
 * Permission to purge an object.
 */
define ('Privilege_purge', 0x40);

/**
 * Permission to secure an object.
 * Currently only used for {@link USER}s and {@link FOLDER}s.
 */
define ('Privilege_secure', 0x80);

/**
 * Permission to upload content.
 * Generally used to determine if a user can create attachments for the object.
 */
define ('Privilege_upload', 0x100);

/**
 * Grant all privileges for an object type.
 */
define ('Privilege_range_object_all', 0x1FF);

/************************************
 * Global privileges
 ************************************/

/**
 * Privileges defined for global options.
 * @see Privilege_offline
 * @see Privilege_subscribe
 * @see Privilege_password
 * @see Privilege_resources
 * @see Privilege_login
 * @see Privilege_search
 */
define ('Privilege_range_global', 'global');

/**
 * Grants access when the application is offline.
 * It is often useful to take the site offline for maintenance, but still be able to acces the
 * site with special users. Users with this privilege can always access the site.
 */
define ('Privilege_offline', 0x01);

/**
 * Permission to change subscriptions.
 * Applies to all subscriptions, regardless of object type.
 */
define ('Privilege_subscribe', 0x02);

/**
 * Permission to change passwords.
 * Applies to all passwords, regardless of object type.
 */
define ('Privilege_password', 0x04);

/**
 * Permission to add/modify resources.
 * Used for icons and themes.
 */
define ('Privilege_resources', 0x08);

/**
 * Permissions to log in.
 */
define ('Privilege_login', 0x10);
/**
 * Permissions to create filters/lists.
 */
define ('Privilege_search', 0x20);
/**
 * Permissions to upgrade/configure application.
 */
define ('Privilege_configure', 0x40);

/**
 * Permission is never granted.
 * Folder/content settings are ignored.
 */
define ('Privilege_always_denied', 0x00);

/**
 * Permission is always granted.
 * Folder/content settings are ignored.
 */
define ('Privilege_always_granted', 0x01);

/**
 * Permission is controlled by content.
 */
define ('Privilege_controlled_by_content', 0x02);

/**
 * User did not specify a preference; {@link History_item_silent} or 
 * {@link History_item_queued} will be used based on context.
 */
define ('History_item_default', 'default');

/**
 * Action was recorded without triggering notifications.
 */
define ('History_item_silent', 'silent');

/**
 * Action was recorded and notifications were sent.
 */
define ('History_item_sent', 'sent');

/**
 * Action was recorded; notifications are pending.
 */
define ('History_item_needs_send', 'queued');

/**
 * Types of history item states.
 * Constant defined for documentation only
 * @see History_item_created
 * @see History_item_updated
 * @see History_item_deleted
 * @see History_item_restored
 * @see History_item_hidden
 * @see History_item_hidden_update
 * @see History_item_published
 * @see History_item_locked
 * @access private
 */
define ('History_item_state_constants', '');

/**
 * Object was created.
 * @access private
 */
define ('History_item_created', 'Created');

/**
 * Object was updated.
 * @access private
 */
define ('History_item_updated', 'Updated');

/**
 * Object was deleted.
 * @access private
 */
define ('History_item_deleted', 'Deleted');

/**
 * Object was restored from deletion or hidden status.
 * @access private
 */
define ('History_item_restored', 'Restored');

/**
 * Object was hidden from non-admin users.
 * @access private
 */
define ('History_item_hidden', 'Hidden');

/**
 * Object was updated while hidden
 * @access private
 */
define ('History_item_hidden_update', 'Hidden update');

/**
 * Object was made visible for all users.
 * Its state changed from {@link Unpublished} to {@link Visible}.
 * @access private
 */
define ('History_item_published', 'Published');

/**
 * Object was revoked (taken back to draft).
 * Its state changed from {@link Visible} to {@link Unpublished}.
 * @access private
 */
define ('History_item_unpublished', 'Unpublished');

/**
 * Object was locked.
 * @access private
 */
define ('History_item_locked', 'Locked');

/**
 * Object was abandoned.
 * Its state changed from {@link Unpublished} to {@link Abandoned}.
 * @access private
 */
define ('History_item_abandoned', 'Abandoned');

/**
 * Object was queued.
 * Its state changed from {@link Unpublished} to {@link Queued}.
 * @access private
 */
define ('History_item_queued', 'Queued');

/**
 * A history item for a {@link USER}.
 * @access private
 */
define ('History_item_user', 'user');

/**
 * A history item for a {@link FOLDER}.
 * @access private
 */
define ('History_item_folder', 'folder');

/**
 * A history item for an {@link ENTRY}.
 * @access private
 */
define ('History_item_entry', 'entry');

/**
 * A history item for a {@link COMMENT}.
 * @access private
 */
define ('History_item_comment', 'comment');

/**
 * A history item for a {@link GROUP}.
 * @access private
 */
define ('History_item_group', 'group');

/**
 * A history item for a {@link ATTACHMENT}.
 * @access private
 */
define ('History_item_attachment', 'attachment');

/**
 * Types of subscriptions available.
 * Constant defined for documentation only
 * @see Subscribe_folder
 * @see Subscribe_entry
 * @see Subscribe_comment
 * @see Subscribe_user
 * @access private
 */
define ('Subscribe_constants', '');

/**
 * Subscription for an entire folder.
 * @access private
 */
define ('Subscribe_folder', 'folder');

/**
 * Subscription for a single entry.
 * @access private
 */
define ('Subscribe_entry', 'entry');

/**
 * Subscription for a single comment.
 * @access private
 */
define ('Subscribe_comment', 'comment');

/**
 * Subscription for all content created by a user.
 * @access private
 */
define ('Subscribe_user', 'user');

/** 
 * Forces full expansion of urls in {@link RESOURCE_MANAGER} functions.
 */
define ('Force_root_on', true);

/** 
 * Prevents full expansion of urls in {@link RESOURCE_MANAGER} functions.
 */
define ('Force_root_off', false);

/**
 * Store logs in this folder.
 * Should resolve to a system-local path. Use as an alias with the {@link
 * RESOURCE_MANAGER}.
 */
define ('Folder_name_logs', 'logs');

/**
 * Special folder that maps to the local file system.
 * Local folders should always use this as a root to avoid mapping to a URL
 * instead (when root expansion is enabled, the system prepends the protocol
 * and domain to resolved file and path names). Use as an alias with the {@link
 * RESOURCE_MANAGER}.
 */
define ('Folder_name_local', 'local');

/**
 * Server system temporary folder.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_system_temp', 'systemp');

/**
 * User data.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_data', 'data');

/**
 * Root of the site managed by the WebCore.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_root', 'root');

/**
 * Base folder for all applications  managed by the WebCore.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_apps', 'apps');

/**
 * Base folder for all resources managed by the WebCore.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_resources', 'resources');

/**
 * Base folder for all pages shared by WebCore applications.
 * Examples are the browser checker, global theme setter and error.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_pages', 'pages');

/**
 * Base folder for all pages representing context-dependent functions. 
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_functions', 'functions');

/**
 * Base folder for icon resources.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_icons', 'icons');

/**
 * Base folder for JavaScript files.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_scripts', 'scripts');

/**
 * Base folder for CSS style sheets.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_styles', 'styles');

/**
 * Base folder for web site themes.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_themes', 'themes');

/**
 * Applications store attachments in a hierarchy under this folder.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_attachments', 'attachments');

/**
 * Base folder for all application-specific resources.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_application', 'app');

/**
 * Base folder for all application-specific icons.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_app_icons', 'app_icons');

/**
 * Base folder for all application-specific styles.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_app_styles', 'app_styles');

/**
 * Base folder for all application-specific scripts.
 * Use as an alias with the {@link RESOURCE_MANAGER}.
 */
define ('Folder_name_app_scripts', 'app_scripts');

/**
 * Increment for adjusting command importance values.
 * Use this value to adjust the importance for a command (e.g.
 * Command_importance_low + Command_importance_increment makes a command that
 * is a bit more likely to be displayed than a low-priority one).
 * @see Command_importance_default
 */
define ('Command_importance_increment', 100);

/**
 * Low sorting weight for a command (prevents display).
 * Used by the UI to determine which commands should be shown in a context.
 * @see Command_importance_default
 */
define ('Command_importance_low', 0);
/**
 * Default sorting weight for a command.
 * Used by the UI to determine which commands should be shown in a context.
 * Use {@link Command_importance_increment} to adjust the low and high
 * constants.
 * @see Command_importance_low
 * @see Command_importance_high
 */
define ('Command_importance_default', 500);

/**
 * High sorting weight for a command (forces display).
 * Used by the UI to determine which commands should be shown in a context.
 * @see Command_importance_default
 */
define ('Command_importance_high', 1000);

/**
 * Show the menu as small as possible.
 * Menu items are only available from a drop-down menu. "Commands" text is
 * hidden.
 * @see Menu_size_standard
 */
define ('Menu_size_minimal', 'minimal');

/**
 * Show the menu without important items.
 * Menu items are only available from a drop-down menu.
 * @see Menu_size_standard
 */
define ('Menu_size_compact', 'compact');

/**
 * Show some, but not all menu items.
 * The most important items are visible, and all menu items are available in a
 * hidden dropdown.
 * @see MENU_RENDERER::set_size()
 * @see menu_size_minimal
 * @see Menu_size_compact
 * @see Menu_size_toolbar
 * @see Menu_size_full
 */
define ('Menu_size_standard', 'standard');

/**
 * Show all menu items as icons only.
 * Does not use a dropdown menu.
 * @see Menu_size_standard
 */
define ('Menu_size_toolbar', 'toolbar');

/**
 * Show all menu items.
 * Does not use a dropdown menu.
 * @see Menu_size_standard
 */
define ('Menu_size_full', 'full');

/**
 * Used to request associated objects from a {@link RENDERABLE}.
 *  This   is a placeholder for documentation only. Do not use this constant.
 * @see Handler_print_renderer
 * @see Handler_html_renderer
 * @see Handler_text_renderer
 * @see Handler_history_item
 * @see Handler_commands
 * @see Handler_menu
 * @see Handler_pdf
 * @see Handler_rss
 * @see Handler_atom
 * @see Handler_navigator
 * @see Handler_subscriptions
 */
define ('Handler_constants', '');

/**
 * Return a print format renderer.
 * @see Handler_constants
 */
define ('Handler_print_renderer', 'print');

/**
 * Return an html format renderer.
 * @see Handler_constants
 */
define ('Handler_html_renderer', 'html');

/**
 * Return a plain text format renderer.
 * @see Handler_constants
 */
define ('Handler_text_renderer', 'text');

/**
 * Return a pdf format renderer.
 * @see Handler_constants
 */
define ('Handler_pdf_renderer', 'pdf');

/**
 * Return an rss format renderer.
 * @see Handler_constants
 */
define ('Handler_rss_renderer', 'rss');

/**
 * Return an atom format renderer.
 * @see Handler_constants
 */
define ('Handler_atom_renderer', 'atom');

/**
 * Return a {@link MAIL_OBJECT_RENDERER}.
 * @see Handler_constants
 */
define ('Handler_mail', 'mail');

/**
 * Return a {@link HISTORY_ITEM}.
 * @see Handler_constants
 */
define ('Handler_history_item', 'history');

/**
 * Return a set of {@link COMMANDS}.
 * @see Handler_constants
 */
define ('Handler_commands', 'commands');

/**
 * Return a {@link MENU_RENDERER}.
 * @see Handler_constants
 */
define ('Handler_menu', 'menu');

/**
 * Return an {@link OBJECT_NAVIGATOR}.
 * @see Handler_constants
 */
define ('Handler_navigator', 'navigator');

/**
 * Return an {@link OBJECT_LOCATION}.
 * @see Handler_constants
 */
define ('Handler_location', 'location');

/**
 * Return an {@link SUBSCRIPTION_RENDERER}.
 * @see Handler_constants
 */
define ('Handler_subscriptions', 'subscriptions');

/**
 * Return a renderer for associated content.
 * This is content that is not shown with the object body by default, but can be
 * requested through this handler. {@link JOB}s show associated {@link CHANGE}s
 * and {@link JOURNAL}s show associated {@link PICTURE}s.
 * @see Handler_constants
 */
define ('Handler_associated_data', 'associated_data');

/**
 * Defines a filter that retrieves all objects.
 */
define ('All', 0xFF);

/**
 * Defines a filter that retrieves no objects.
 */
define ('None', 0x00);

/**
 * Normal state for an object.
  * User must have 'View_content' and 'View_folder' rights to see this object.
  * @access private
  */
define ('Visible', 0x01);

/**
 * Object is visible, but not modifiable.
 * Interpretation of the locked flag is up to the individual object.
 * @access private
 */
define ('Locked', 0x05);

/**
 * Marks deleted, hidden or special case objects.
 * Can be combined with other flags to indicate specialized visible states (like
 * 'Draft' in the News module). User must have 'View_invisible', 'View_content'
 * and 'View_folder' rights to see this object.
 * @access private
 */
define ('Invisible', 0x02);

/**
 * Marks an object as deleted.
 */
define ('Deleted', 0x06);

/**
 * Marks an object as hidden.
 * This allows two levels of objects, with the 'View_invisible' right granted to
 * moderator or administrator users.
 */
define ('Hidden', 0x0A);

/**
 * Entry is not ready for general viewing.
 * Mark objects as unpublished so that users can keep work-in-progress in the
 * database without displaying it. Users with "hidden" privileges will see all
 * unpublished work from all users.
 */
define ('Unpublished', 0x12);

/**
 * Entry is in-progress.
 * Specialization of {@link Unpublished}. Entries are in this state when
 * created.
 */
define ('Draft', 0x32);

/**
 * Entry is ready for review and publishing.
 * Specialization of {@link Unpublished}. Mark objects as queued when a draft is
 * ready to publish. This adds it to the publishing queue; users with rights to
 * publish objects can make it {@link Visible}.
 */
define ('Queued', 0x52);

/**
 * Entry was not (and will not be) published.
 * Mark objects as abandoned if they are to be permanently archived drafts. This
 * is a useful state for notes that never got published, but should not be
 * deleted.
 */
define ('Abandoned', 0x92);

/**
 * Messages from the {@link PUBLISHER} are sent on this channel.
 * Used with {@link log_message()}.
 * @access private
 */
define ('Msg_channel_publisher', 'Publisher');

/**
 * Messages from {@link MAIL_PROVIDER}s are sent on this channel.
 * Used with {@link log_message()}.
 * @access private
 */
define ('Msg_channel_mail', 'Mail');

/**
 * Messages from the {@link NEWSFEED_ENGINE} are sent on this channel.
 * Used with {@link log_message()}.
 * @access private
 */
define ('Msg_channel_newsfeed', 'Newsfeed');

/**
 * Used with a {@link NEWSFEED_ENGINE}.
 * This is a placeholder for documentation only. Do not use this constant.
 * 
 * @see Newsfeed_format_rss
 * @see Newsfeed_format_atom
 * @see Newsfeed_content_html
 * @see Newsfeed_content_text
 * @see Newsfeed_content_full_html
 */
define ('Newsfeed_constants', '');

/**
 * Used by {@link NEWSFEED_ENGINE::make_renderer()}.
 */
define ('Newsfeed_format_rss', 'rss');

/**
 * Used by {@link NEWSFEED_ENGINE::make_renderer()}.
 */
define ('Newsfeed_format_atom', 'atom');

/**
 * Newsfeed articles are rendered as HTML fragments.
 * 
 * Used by {@link NEWSFEED_ENGINE::make_renderer()}.
 */
define ('Newsfeed_content_html', 'html');

/**
 * Newsfeed articles are rendered as valid and complete HTML documents.
 * 
 * Used by {@link NEWSFEED_ENGINE::make_renderer()}.
 */
define ('Newsfeed_content_full_html', 'full_html');

/**
 * Used by {@link NEWSFEED_ENGINE::make_renderer()}.
 */
define ('Newsfeed_content_text', 'text');

define ('Sixteen_px', '16px');
define ('Fifteen_px', '15px');
define ('Twenty_px', '20px');
define ('Thirty_px', '30px');
define ('Thirty_two_px', '32px');
define ('Fifty_px', '50px');
define ('One_hundred_px', '100px');