import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

import NavBarList         from './NavBarList';
import NavBarDropdown     from './NavBarDropdown';
import NavBarDropdownItem from './NavBarDropdownItem';
import NavBarLink         from './NavBarLink';
import NavBarSearchBar    from './NavBarSearchBar';

import NavBarModalLink          from './Admin/NavBarModalLink';
import WikiPageCreationForm     from './Admin/WikiPageCreationForm';
import WikiPageModificationForm from './Admin/WikiPageModificationForm';
import WikiPageDeletionForm     from './Admin/WikiPageDeletionForm';

export default function NavBarContent()
{
	const globals = useContext(GlobalsContext);

	return (
		<div id="navbar-content-wrapper">
			<div id="navbar-content">
				<NavBarList>
					<NavBarDropdown href="/#locations" text="locations" active={ false }>
						<NavBarDropdownItem href="/#kingdom-of-guthan" text="The Kingdom of Guthan" />
						<NavBarDropdownItem href="/#the-valen-ministry" text="The Valen Ministry" />
						<NavBarDropdownItem href="/#draakna" text="Dra'akna" />
					</NavBarDropdown>
						{
							globals.isAuthenticated ? (
								<>
									<NavBarModalLink linkText='Add Page' ChildComponent={ WikiPageCreationForm }/>
									<NavBarModalLink linkText='Edit Page' ChildComponent={ WikiPageModificationForm }/>
									<NavBarModalLink linkText='Delete Page' ChildComponent={ WikiPageDeletionForm }/>
								</>
							) : (<React.Fragment />)
						}
				</NavBarList>
				<NavBarList position="right">
					<NavBarSearchBar />
					{
						globals.isAuthenticated
						? (
							<NavBarDropdown href="#" 
							text={
								(globals.preferredName)
								? globals.preferredName 
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
					}
				</NavBarList>
			</div>
		</div>
	);
}
