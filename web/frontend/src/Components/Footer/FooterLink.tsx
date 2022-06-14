import React, { ReactElement } from 'react';

interface Props
{
	href: string;
	text: string;
};

export const FooterLink = ({ href, text }: Props): ReactElement =>
{
	return (
		<div className="footer-link">
			<a className="underline-above" href={ href }>
				{ text }
			</a>
		</div>
	);
};
