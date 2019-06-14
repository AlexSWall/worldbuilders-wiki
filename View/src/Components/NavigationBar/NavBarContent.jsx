import React, { Component } from 'react';

import NavBarList         from './NavBarList';
import NavBarDropdown     from './NavBarDropdown';
import NavBarDropdownItem from './NavBarDropdownItem';
import NavBarButton       from './NavBarButton';
import NavBarSearchBar    from './NavBarSearchBar';

class NavBarContent extends Component 
{	
	render()
	{
		const authenticationData = this.props.authenticationData;
		return (
			<div id="navbar-content-wrapper">
				<div id="navbar-content">
					<NavBarList>
						<NavBarDropdown href="/#Locations" text="Locations" active={false}>
							<NavBarDropdownItem href="/#Guthan" text="The Kingdom of Guthan" />
							<NavBarDropdownItem href="/#The_Valen_Ministry" text="The Valen Ministry" />
							<NavBarDropdownItem href="/#Dra'akna" text="Dra'akna" />
						</NavBarDropdown>
						<NavBarButton href="/#Special:Add_Wiki_Page" text="Add Page" active={false} />
						<NavBarButton href="/#Special:Edit_Wiki_Page" text="Edit Page" active={false} />
						<NavBarButton href="/#About" text="About" active={false} />
					</NavBarList>
					<NavBarList position="right">
						<NavBarSearchBar />
						{authenticationData.isAuthenticated ? (
							<NavBarDropdown href="#" 
								text={
									(authenticationData.userData.preferredName)
									? authenticationData.userData.preferredName 
									: 'Account'
								} active={false}>
								<NavBarDropdownItem href="/Change_Password" text="Change Password" />
								<NavBarDropdownItem href="/Sign_Out" text="Sign Out" />
							</NavBarDropdown>
						) : (
							<React.Fragment>
								<NavBarButton href="/Sign_Up" text="Sign up" active={false} />
								<NavBarButton href="/Sign_In" text="Sign in" active={false} />
							</React.Fragment>
						) }
					</NavBarList>
				</div>
			</div>
		);
	}
}

export default NavBarContent;