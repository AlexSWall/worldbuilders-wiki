import React, { Component } from 'react';

import CSRFField from 'Form Components/CSRFField'

class NavBarSearchBar extends Component 
{
	render() {
		return (
			<li className="navbar-item">
				<div className="searchbar-wrapper">
					<form action='Search' method='get' autoComplete='off' className="searchbar">
						<input type="text" className="searchbar-input" placeholder="What are you looking for?" />
						<button type="submit" className="searchbar-button">
							<svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false" style={ {'pointerEvents': 'none', display: 'block', width: '100%', height: '100%'} }>
								<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
							</svg>
						</button>
						<CSRFField csrfHTML={this.props.csrfHTML} />
					</form>
				</div>
			</li>
		);
	}
}

export default NavBarSearchBar;