<?php
/**
 * Template: layout
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\Utils\Layout_Utils;

$yaml_output = Layout_Utils::export_to_string();

if ( false === $yaml_output ) {
	return;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="yaml-container">
		<div class="yaml-header">
			<button type="button" class="button button-secondary" onclick="copyYamlContent()">
				Copy YAML
			</button>
		</div>
		<div id="dashmate-layout-content">
			<pre><code><?php echo $yaml_output; ?></code></pre>
		</div>
	</div>
</div>

<style>
.yaml-container {
	margin-top: 20px;
}

.yaml-header {
	margin-bottom: 10px;
}

#dashmate-layout-content {
	background: #f6f8fa;
	border: 1px solid #e1e4e8;
	border-radius: 6px;
	padding: 16px;
	overflow-x: auto;
}

#dashmate-layout-content pre {
	margin: 0;
	white-space: pre-wrap;
	word-wrap: break-word;
	font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
	font-size: 13px;
	line-height: 1.45;
}

#dashmate-layout-content code {
	background: transparent;
	padding: 0;
	border: none;
}
</style>

<script>
function copyYamlContent() {
	const yamlElement = document.getElementById('dashmate-layout-content');
	const codeElement = yamlElement.querySelector('code');

	if ( codeElement ) {
		const yamlText = codeElement.textContent || codeElement.innerText;

		// Create a temporary textarea to copy the content
		const textarea = document.createElement('textarea');
		textarea.value = yamlText;
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		document.body.appendChild(textarea);

		// Select and copy the text
		textarea.select();
		textarea.setSelectionRange(0, 99999); // For mobile devices

		try {
			document.execCommand('copy');

			// Show success feedback
			const button = event.target;
			const originalText = button.textContent;
			button.textContent = 'Copied!';
			button.style.backgroundColor = '#46b450';
			button.style.color = '#fff';

			setTimeout(() => {
				button.textContent = originalText;
				button.style.backgroundColor = '';
				button.style.color = '';
			}, 2000);

		} catch ( err ) {
			console.error('Failed to copy: ', err);

			// Fallback for modern browsers
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText(yamlText).then(() => {
					const button = event.target;
					const originalText = button.textContent;
					button.textContent = 'Copied!';
					button.style.backgroundColor = '#46b450';
					button.style.color = '#fff';

					setTimeout(() => {
						button.textContent = originalText;
						button.style.backgroundColor = '';
						button.style.color = '';
					}, 2000);
				}).catch(err => {
					console.error('Clipboard API failed: ', err);
				});
			}
		}

		// Clean up
		document.body.removeChild(textarea);
	}
}
</script>
