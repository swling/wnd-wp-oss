<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 *@since 2019.01.16 转移option选项
 *author：swling 
 *email：tangfou@gmail.com
 */
if (!get_option('wndoss')) {
	update_option('wndoss', get_option('wndoss_options'), 'no');
} else {
	delete_option('wndoss_options');
}

/**
 *@since 2019.01.16 注册后台设置菜单
 */
function wndoss_register_options_page()
{
	add_options_page('Wnd OSS Setting', 'Wnd OSS', 'manage_options', 'wndoss', 'wndoss_options');
}
add_action('admin_menu', 'wndoss_register_options_page');

/**
 *@since 2019.01.16
 *设置表单
 */
function wndoss_options()
{

	if ($_POST && current_user_can('administrator')) {

		// 安全检查
		check_admin_referer('wndoss_update');

		// 按前缀筛选数组,过滤掉非指定数据和空值
		foreach ($_POST as $key => $value) {
			if (strpos($key, 'wndoss_') === false or $value == '') {
				unset($_POST[$key]);
			}

			$_POST[$key] = trim($value);
		}
		unset($key, $value);

		// 更新设置
		update_option('wndoss', $_POST);

		echo '<div class="updated settings-error"><p>更新成功！</p></div>';
	}

	$options = get_option('wndoss');

	?>

	<div class="wrap">
		<form method="post" action="">
			<table class="form-table">

				<!--短信设置-->
				<tr>
					<th valign="top">
						OSS配置
					</th>
				</tr>
				<tr>
					<td valign="top">本地保留文件</td>
					<td>
						<input type="checkbox" name="wndoss_local_storage" value="1" <?php if (isset($options['wndoss_local_storage']) and $options['wndoss_local_storage'] == 1) echo 'checked'; ?>>
						<p><i>是否在本地服务器上保留上传文件</i></p>
					</td>
				</tr>
				<tr>
					<td valign="top">OSS accessKeyId</td>
					<td>
						<input type="text" name="wndoss_access_key_id" value="<?php echo $options['wndoss_access_key_id'] ?? ''; ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<td valign="top">OSS accessKeySecrety</td>
					<td>
						<input type="text" name="wndoss_access_key_secret" value="<?php echo $options['wndoss_access_key_secret'] ?? ''; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<td valign="top">OSS Endpoint</td>
					<td>
						<input type="text" name="wndoss_endpoint" value="<?php echo $options['wndoss_endpoint'] ?? ''; ?>" class="regular-text">
						<p><i>*服务器与OSS处于同一区域可填内网地址，否则请填公网地址(无需添加 http:// 前缀)<i></p>
					</td>
				</tr>

				<tr>
					<td valign="top">OSS Bucket名称</td>
					<td>
						<input type="text" name="wndoss_bucket" value="<?php echo $options['wndoss_bucket'] ?? ''; ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<td valign="top">OSS Bucket路径</td>
					<td>
						<input type="text" name="wndoss_bucket_path" value="<?php echo $options['wndoss_bucket_path'] ?? ''; ?>" class="regular-text">
						<p><i>*本应用上传的文件对应在OSS中的存储路径，留空为根目录<i></p>
					</td>
				</tr>

				<tr>
					<td valign="top">OSS base url</td>
					<td>
						<input type="text" name="wndoss_baseurl" value="<?php echo $options['wndoss_baseurl'] ?? ''; ?>" class="regular-text">
						<p><i>*OSS文件公网访问路径。通常为：Bucket域名 + 本应用对应的存储路径<i></p>
					</td>
				</tr>

				<!--CDN设置-->
				<tr>
					<th valign="top">
						CDN配置
					</th>
				</tr>

				<tr>
					<td valign="top">启用CDN</td>
					<td>
						<input type="checkbox" name="wndoss_enable_cdn" value="1" <?php if (isset($options['wndoss_enable_cdn']) and $options['wndoss_enable_cdn'] == 1) echo 'checked'; ?>>
					</td>
				</tr>

				<tr>
					<td valign="top">本地URL</td>
					<td>
						<input type="text" name="wndoss_site_url" value="<?php echo $options['wndoss_site_url'] ?? ''; ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<td valign="top">CDN URL</td>
					<td>
						<input type="text" name="wndoss_cdn_url" value="<?php echo $options['wndoss_cdn_url'] ?? ''; ?>" class="regular-text">
						<p><i>*将以此替换静态资源的本地URL<i></p>
					</td>
				</tr>

				<tr>
					<td valign="top">CDN 目录</td>
					<td>
						<input type="text" name="wndoss_cdn_dirs" value="<?php echo $options['wndoss_cdn_dirs'] ?? ''; ?>" class="regular-text">
						<p><i>*哪些目录的具体资源需要设置CDN，通常为wp-content / wp-includes<i></p>
					</td>
				</tr>

				<tr>
					<td valign="top">CDN 文件后缀排除</td>
					<td>
						<input type="text" name="wndoss_cdn_excludes" value="<?php echo $options['wndoss_cdn_excludes'] ?? ''; ?>" class="regular-text">
						<p><i>*以逗号区分如：.php, .flv, .do<i></p>
					</td>
				</tr>

			</table>
			<?php wp_nonce_field('wndoss_update'); ?>
			<input type="submit" value="保存设置" class="button-primary" />
		</form>
	</div>

<?php }
