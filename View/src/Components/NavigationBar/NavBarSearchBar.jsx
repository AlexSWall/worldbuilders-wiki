import React, { Component } from 'react';

class NavBarSearchBar extends Component 
{
	render() {
		return (
			<li className="navbar-item">
				<div className="searchbar-wrapper">
					<div className="searchbar">
						<input type="text" className="searchbar-input" placeholder="What are you looking for?" />
						<button type="submit" className="searchbar-button">
							<i className="fa fa-search"></i>
						</button>
					</div>
				</div>
			</li>
		);
	}
}

export default NavBarSearchBar;

