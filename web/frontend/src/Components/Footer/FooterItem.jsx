import React, { Component } from 'react';

class FooterItem extends Component 
{	
	render() {
		return (
			<div id="rightFooterLineItem">
				<a className="footerLink" href={this.props.href}>
					{this.props.text}
				</a>
			</div>
		);
	}
}

export default FooterItem;