import React, { useContext } from 'react';

import GlobalsContext from 'GlobalsContext';

import DropdownList from './NavigationBar/DropdownList';
import DropdownItem from './NavigationBar/DropdownItem';
import Item         from './NavigationBar/Item';
import SearchBar    from './NavigationBar/SearchBar';

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
		<div id="navbar-wrapper">
			<nav id="navbar">
				<div id="navbar-brand-wrapper">
					<a id="navbar-brand" href="/#">
						Weavemajj
					</a>
				</div>
				<div id="navbar-content">
					<ul className="navbar-list">
						<Item text='Wiki Navigation' href={ false }>
							<DropdownList>
								<DropdownItem text='Cosmology'>
									<DropdownList>
										<DropdownItem text='Grand History' />
										<DropdownItem text='Deities and Religion' />
										<DropdownItem text='The Planes' />
										<DropdownItem text='Magic and Natural Sciences' />
									</DropdownList>
								</DropdownItem>
								<DropdownItem text='Locations'>
									<DropdownList>
										<DropdownItem text="Tal'Dorei" />
										<DropdownItem text='Wildemount' />
										<DropdownItem text='Outer Planes' />
									</DropdownList>
								</DropdownItem>
								<DropdownItem text='Miscellaneous'>
									<DropdownList>
										<DropdownItem text='Misconceptions' />
									</DropdownList>
								</DropdownItem>
								<DropdownItem text='Random Page' onClick={ () => {
									return false;
								} }/>
							</DropdownList>
						</Item>
						{
							globals.isAuthenticated ? (
								<>
									<Item text='Add Page' ModalComponent={ WikiPageCreationForm } />
									<Item text='Edit Page' ModalComponent={ WikiPageModificationForm } />
									<Item text='Delete Page' ModalComponent={ WikiPageDeletionForm } />
								</>
							) : (<React.Fragment />)
						}
					</ul>
					<ul className="navbar-list navbar-list-right">
						<SearchBar />
						{
							globals.isAuthenticated
							? (
								<Item text={ globals.preferredName || 'Account' } href={ false }>
									<DropdownList>
										<DropdownItem text='Change Password' ModalComponent={ ChangePasswordForm } />
										<DropdownItem text='Sign Out' onClick={ () => signOut(globals.csrfTokens) } />
									</DropdownList>
								</Item>
							) : (
								<>
									<Item text='Sign up' ModalComponent={ SignUpForm } />
									<Item text='Sign in' ModalComponent={ SignInForm } />
								</>
							)
						}
					</ul>
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
