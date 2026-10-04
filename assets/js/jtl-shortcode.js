(function () {
	'use strict';

	/**
	 * 初始化頁面上的所有問候 Shortcode 元件。
	 *
	 * @return {void}
	 */
	function initializeGreetingComponents() {
		const components = document.querySelectorAll( '[data-jtl-greeting]' );

		components.forEach( function ( component ) {
			const toggle = component.querySelector( '[data-jtl-toggle]' );
			const details = component.querySelector( '[data-jtl-details]' );

			if ( ! toggle || ! details ) {
				return;
			}

			toggle.addEventListener( 'click', function () {
				const isExpanded = 'true' === toggle.getAttribute( 'aria-expanded' );

				toggle.setAttribute( 'aria-expanded', String( ! isExpanded ) );
				details.hidden = isExpanded;
				toggle.textContent = isExpanded ? '顯示說明' : '隱藏說明';
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initializeGreetingComponents );
	} else {
		initializeGreetingComponents();
	}
}());
