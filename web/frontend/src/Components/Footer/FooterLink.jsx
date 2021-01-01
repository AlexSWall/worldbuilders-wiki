import React from 'react';

export default function FooterLink({ href, text })
{
	return (
		<a className="footer-link" href={ href }>
			{ text }
		</a>
	);
}
