import './css/common.css';

const goOpenLinks = () => {
	if ( ! linkitLocalized?.open_links || ! linkitLocalized?.all_links ) return;

	linkitLocalized.open_links.forEach( ( linkId ) => {
		const url = linkitLocalized.all_links[ linkId ]?.url;
		if ( url ) window.open( url, '_blank' );
	} );
};

document.addEventListener( 'DOMContentLoaded', () => {
	const btnOpener = document.querySelector( '#btn-dm-open-links' );

	if ( btnOpener ) {
		btnOpener.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			goOpenLinks();
		} );
	}
} );
