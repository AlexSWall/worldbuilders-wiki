import React from 'react';

export default function FooterLink({ href, text })
{
	return (
		<div className="footer-link">
			<a className="underline-above" href={ href }>
				{ text }
			</a>
		</div>
	);
}
