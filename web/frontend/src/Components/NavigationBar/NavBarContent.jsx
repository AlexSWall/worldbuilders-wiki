import React from 'react';

import { GlobalConsumer } from '../../GlobalStore';

import NavBarList         from './NavBarList';
import NavBarDropdown     from './NavBarDropdown';
import NavBarDropdownItem from './NavBarDropdownItem';
import NavBarLink         from './NavBarLink';
import NavBarSearchBar    from './NavBarSearchBar';

import CreateWikiPageNavBarLink from './Admin/CreateWikiPageNavBarLink';

export default function NavBarContent()
{
	return (
		<div id="navbar-content-wrapper">
			<div id="navbar-content">
				<NavBarList>
					<NavBarDropdown href="/#Locations" text="Locations" active={ false }>
						<NavBarDropdownItem href="/#Guthan" text="The Kingdom of Guthan" />
						<NavBarDropdownItem href="/#The_Valen_Ministry" text="The Valen Ministry" />
						<NavBarDropdownItem href="/#Dra'akna" text="Dra'akna" />
					</NavBarDropdown>
					<GlobalConsumer>
						{ globals => (
							globals.authData.isAuthenticated ? (
								<>
									<CreateWikiPageNavBarLink />
									<NavBarLink onClick={ (_e) => window.location.hash = window.location.hash.split('?')[0] + "?action=edit" } text="Edit Page" active={ false } />
								</>
							) : (<React.Fragment />)
						) }
					</GlobalConsumer>
				</NavBarList>
				<NavBarList position="right">
					<NavBarSearchBar />
					<GlobalConsumer>
						{ globals => (
							globals.authData.isAuthenticated ? (
								<NavBarDropdown href="#" 
									text={
										(globals.authData.userData.preferredName)
										? globals.authData.userData.preferredName 
										: 'Account'
									} active={ false }>
									<NavBarDropdownItem href="/Change_Password" text="Change Password" />
									<NavBarDropdownItem href="/Sign_Out" text="Sign Out" />
								</NavBarDropdown>
							) : (
								<React.Fragment>
									<NavBarLink href="/Sign_Up" text="Sign up" active={ false } />
									<NavBarLink href="/Sign_In" text="Sign in" active={ false } />
								</React.Fragment>
							)
						) }
					</GlobalConsumer>
				</NavBarList>
			</div>
		</div>
	);
}
