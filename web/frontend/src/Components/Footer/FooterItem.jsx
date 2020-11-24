import React from 'react';

export default function FooterItem({ href, text })
{
	return (
		<div id="rightFooterLineItem">
			<a className="footerLink" href={ href }>
				{ text }
			</a>
		</div>
	);
}
