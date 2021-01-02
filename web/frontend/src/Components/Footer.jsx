import React from 'react';

import FooterLink from './Footer/FooterLink';

export default function Footer() 
{
	return (
		<footer>
			<div id="footer-content">
				<div className="footer-item" style={ { fontSize: '13rem', letterSpacing: '1rem' } }>
					The Worldbuilder's Wiki
				</div>
				<div className="footer-item">
					<FooterLink href="/#about" text="About" />
					<FooterLink href="/#privacy-policy" text="Privacy Policy" />
					<FooterLink href="/#terms-of-service" text="Terms of Service" />
					<FooterLink href="/#contact-and-feedback" text="Contact & Feedback" />
					<FooterLink href="/#bugs" text="Known Bugs" />
				</div>
			</div>
		</footer>
	);
}
