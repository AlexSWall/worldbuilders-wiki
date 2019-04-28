import React, { Component } from 'react';

import NavBarList         from './NavBarList';
import NavBarDropdown     from './NavBarDropdown';
import NavBarDropdownItem from './NavBarDropdownItem';
import NavBarButton       from './NavBarButton';

class NavBarContent extends Component 
{	
	render()
	{
		const auth = this.props.auth;
		return (
			<div id="navbar-content-wrapper">
				<div id="navbar-content">
					<NavBarList>
						<NavBarDropdown href="Locations" text="Locations" active={false}>
							<NavBarDropdownItem href="Guthan" text="The Kingdom of Guthan" />
							<NavBarDropdownItem href="The_Valen_Ministry" text="The Valen Ministry" />
							<NavBarDropdownItem href="Dra'akna" text="Dra'akna" />
						</NavBarDropdown>
						<NavBarButton href="Add_Page" text="Add Page" active={false} />
						<NavBarButton href="About" text="About" active={false} />
					</NavBarList>
					<NavBarList position="right">
						{auth.check ? (
							auth.user.permissions.is_admin ? (
								<NavBarButton href="{{ path_for('admin.home') }}" text="Administration" active={false} />
							) : (
								<NavBarDropdown href="#" 
									text={auth.user.details.preferred_name ? auth.user.details.preferred_name : Account} 
									active={false}>
									<NavBarDropdownItem href="{{ path_for('auth.password.change') }}" text="Change Password" />
									<NavBarDropdownItem href="{{ path_for('auth.signout') }}" text="Sign Out" />
								</NavBarDropdown>
							)
						) : (
							<React.Fragment>
								<NavBarButton href="{{ path_for('auth.signup') }}" text="Sign up" active={false} />
								<NavBarButton href="{{ path_for('auth.signin') }}" text="Sign in" active={false} />
							</React.Fragment>
						) }
					</NavBarList>
				</div>
			</div>
		);
	}
}

export default NavBarContent;

/* const buttons = {
	addpage: {
		href: "Add_Page",
		text: "Add Page",
		active: {false}
	},
	about: {
		href: "About",
		text: "About",
		active: {false}
	}
};

this.buttons.map({ href, text, active } => (
	<NavBarButton href={href} text={text} active={active} />
)); */
