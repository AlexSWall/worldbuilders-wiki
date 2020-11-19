import React, { Component } from 'react';

import NavBarList         from './NavBarList';
import NavBarDropdown     from './NavBarDropdown';
import NavBarDropdownItem from './NavBarDropdownItem';
import NavBarLink       from './NavBarLink';
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
						<NavBarLink href="/#Special:Add_Wiki_Page" text="Add Page" active={false} />
						<NavBarLink href="/#Special:Edit_Wiki_Page" text="Edit Page" active={false} />
						<NavBarLink href="/#About" text="About" active={false} />
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
								<NavBarLink href="/Sign_Up" text="Sign up" active={false} />
								<NavBarLink href="/Sign_In" text="Sign in" active={false} />
							</React.Fragment>
						) }
					</NavBarList>
				</div>
			</div>
		);
	}
}

export default NavBarContent;