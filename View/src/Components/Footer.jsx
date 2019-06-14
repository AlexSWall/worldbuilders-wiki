import React, { Component } from 'react';

import FooterItem from './Footer/FooterItem';

class Footer extends Component 
{
	render() {
		return (
			<div id="footerWrapper">
				<footer>
					<div id="footerContent">
						<div id="leftFooterContent">
							<div id="leftFooterLine">
								<div id="leftFooterLineItem">
									Alex's World's D<small>&</small>D Wiki
								</div>
								<div id="leftFooterLineItem">
									Dungeons & Dragons is property of Wizards of the Coast LLC. © 2019 Wizards.
								</div>
							</div>
						</div>
						<div id="rightFooterContent">
							<div id="rightFooterLine">
								<FooterItem href="/#About" text="About" />
								<FooterItem href="/#Contact" text="Contact" />
								<FooterItem href="/#Bugs" text="Known Bugs" />
							</div>
						</div>
					</div>
				</footer>
			</div>
		);
	}
}

export default Footer;