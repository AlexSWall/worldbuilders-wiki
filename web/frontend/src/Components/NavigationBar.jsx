import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

import NavBarDropdown     from './NavigationBar/NavBarDropdown';
import NavBarDropdownItem from './NavigationBar/NavBarDropdownItem';
import NavBarModalLink    from './NavigationBar/NavBarModalLink';
import NavBarSearchBar    from './NavigationBar/NavBarSearchBar';

import SignInForm               from './ModalForms/SignInForm';
import SignUpForm               from './ModalForms/SignUpForm';
import ChangePasswordForm       from './ModalForms/ChangePasswordForm';
import WikiPageCreationForm     from './ModalForms/WikiPageCreationForm';
import WikiPageModificationForm from './ModalForms/WikiPageModificationForm';
import WikiPageDeletionForm     from './ModalForms/WikiPageDeletionForm';

export default function NavigationBar()
{
	const globals = useContext(GlobalsContext);

	return (
		<div id="navbarWrapper">
			<nav id="navbar">
				<div id="navbar-brand-wrapper">
					<a id="navbar-brand" href="/#">
						My Website
					</a>
				</div>
				<div id="navbar-content-wrapper">
					<div id="navbar-content">
						<ul className="navbar-list">
							<NavBarDropdown href="/#locations" text="Locations" active={ false }>
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
						</ul>
						<ul className="navbar-list navbar-list-right">
							<NavBarSearchBar />
							{
								globals.isAuthenticated
								? (
									<NavBarDropdown href="#"
										text={
											(globals.preferredName)
											? globals.preferredName
											: 'Account' }
										active={ false }
									>
										<NavBarDropdownItem text='Change Password' ModalComponent={ ChangePasswordForm } />
										<NavBarDropdownItem onClick={ () => signOut(globals.csrfTokens) } text="Sign Out" />
									</NavBarDropdown>
								) : (
									<>
										<NavBarModalLink linkText='Sign up' ChildComponent={ SignUpForm }/>
										<NavBarModalLink linkText='Sign in' ChildComponent={ SignInForm }/>
									</>
								)
							}
						</ul>
					</div>
				</div>
			</nav>
		</div>
	);
}

function signOut(csrfTokens)
{
	fetch('/auth/', {
		method: 'post',
		headers: {
			'Accept': 'application/json, text/plain, */*',
			'Content-Type': 'application/json'
		},
		body: JSON.stringify(Object.assign({}, {
			action: 'sign out',
			data: {},
		}, csrfTokens))
	}).then(async res => {
		if (res.ok)
		{
			location.reload();
		}
		else
		{
			console.log('Error: Received status code ' + res.status + ' in response to POST request');

			const contentType = res.headers.get("content-type");

			if (contentType && contentType.indexOf("application/json") !== -1) {
				res.json().then(data => {
					console.log('Error: ' + data.error);
				});
			} else {
				res.text().then(text => {
					console.log('Error (text): ' + text);
				});
			}
		}
	}).catch( error => {
		console.log('Failed to make POST request...')
		console.log(error);
	});
}
