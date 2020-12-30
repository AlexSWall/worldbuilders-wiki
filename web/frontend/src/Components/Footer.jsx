import React from 'react';

import FooterItem from './Footer/FooterItem';

export default function Footer() 
{
	return (
		<div id="footerWrapper">
			<footer>
				<div id="footerContent">
					<div id="leftFooterContent">
						<div id="leftFooterLine">
							<div id="leftFooterLineItem">
								The Weavemajj Campaign Setting
							</div>
							<div id="leftFooterLineItem">
								Dungeons & Dragons is property of Wizards of the Coast LLC.
							</div>
						</div>
					</div>
					<div id="rightFooterContent">
						<div id="rightFooterLine">
							<FooterItem href="/#about" text="About" />
							<FooterItem href="/#privacy-policy" text="Privacy Policy" />
							<FooterItem href="/#terms-of-service" text="Terms of Service" />
							<FooterItem href="/#contact-and-feedback" text="Contact & Feedback" />
							<FooterItem href="/#bugs" text="Known Bugs" />
						</div>
					</div>
				</div>
			</footer>
		</div>
	);
}
