import React, { Component } from 'react';

class FooterItem extends Component 
{	
	render() {
		return (
			<li>
				<a className="footerLink" href={this.props.href}>
					{this.props.text}
				</a>
			</li>
		);
	}
}

export default FooterItem;