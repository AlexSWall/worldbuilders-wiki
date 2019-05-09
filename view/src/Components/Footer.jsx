import React, { Component } from 'react';

import FooterItem from './Footer/FooterItem';

class Footer extends Component 
{
	render() {
		return (
			<div id="footerWrapper">
				<footer>
					<ul>
						<FooterItem href="About" text="About" />
						<FooterItem href="Contact" text="Contact" />
						<FooterItem href="Bugs" text="Known Bugs" />
					</ul>
				</footer>
			</div>
		);
	}
}

export default Footer;