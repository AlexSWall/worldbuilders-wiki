import React, { ReactElement, useContext } from 'react';

import { GlobalStateContext, GlobalStateDispatchContext, SET_LEFT_SIDEBAR } from 'GlobalState';

import { DropdownList }          from './NavigationBar/DropdownList';
import { HamburgerToggleButton } from './NavigationBar/HamburgerToggleButton';
import { HrefItem }              from './NavigationBar/Items/HrefItem';
import { ModalItem }             from './NavigationBar/Items/ModalItem';
import { NullItem }              from './NavigationBar/Items/NullItem';
import { OnClickItem }           from './NavigationBar/Items/OnClickItem';
import { SearchBar }             from './NavigationBar/SearchBar';

import { ChangePasswordForm }       from './ModalForms/ChangePasswordForm';
import { InfoboxCreationForm }      from './ModalForms/InfoboxCreationForm';
import { InfoboxDeletionForm }      from './ModalForms/InfoboxDeletionForm';
import { InfoboxModificationForm }  from './ModalForms/InfoboxModificationForm';
import { WikiPageCreationForm }     from './ModalForms/WikiPageCreationForm';
import { WikiPageDeletionForm }     from './ModalForms/WikiPageDeletionForm';
import { WikiPageModificationForm } from './ModalForms/WikiPageModificationForm';
import { SignInForm }               from './ModalForms/SignInForm';
import { SignUpForm }               from './ModalForms/SignUpForm';
import { makeApiPostRequest } from 'utils/api';

export const NavigationBar = (): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );
	const globalDispatch = useContext( GlobalStateDispatchContext );

	return (
		<div id="navbar-wrapper">
			<nav id="navbar">
				<ul className="navbar-list">
					{
						globalState.isAuthenticated ? (
							<HamburgerToggleButton
								isOnFn={ isOn => globalDispatch({ type: SET_LEFT_SIDEBAR, panel: isOn ? 'QuickNavigator' : null }) }
							/>
						) : (<React.Fragment />)
					}
				</ul>
				<div id="navbar-brand-wrapper">
					<a id="navbar-brand" href="/#">
						Weavemajj
					</a>
				</div>
				<div id="navbar-content">
					<ul className="navbar-list">
						<NullItem text='Wiki Navigation'>
							<DropdownList>
								<HrefItem type='dropdown' text='Cosmology'>
									<DropdownList>
										<HrefItem type='dropdown' text='Grand History' />
										<HrefItem type='dropdown' text='Deities and Religion' />
										<HrefItem type='dropdown' text='The Planes' />
										<HrefItem type='dropdown' text='Magic and Natural Sciences' />
									</DropdownList>
								</HrefItem>
								<HrefItem type='dropdown' text='Locations'>
									<DropdownList>
										<HrefItem type='dropdown' text="Tal'Dorei" />
										<HrefItem type='dropdown' text='Wildemount' />
										<HrefItem type='dropdown' text='Outer Planes' />
									</DropdownList>
								</HrefItem>
								<NullItem type='dropdown' text='Miscellaneous'>
									<DropdownList>
										<HrefItem type='dropdown' text='Misconceptions' />
									</DropdownList>
								</NullItem>
								<OnClickItem type='dropdown' text='Random Page' onClick={ () => {
									return false;
								} }/>
							</DropdownList>
						</NullItem>
						{
							globalState.isAuthenticated ? (
								<>
									<NullItem text='Modify Wiki'>
										<DropdownList>
											<ModalItem type='dropdown' text='Add Page' ModalComponent={ WikiPageCreationForm } />
											<ModalItem type='dropdown' text='Edit Page' ModalComponent={ WikiPageModificationForm } />
											<ModalItem type='dropdown' text='Delete Page' ModalComponent={ WikiPageDeletionForm } />
											<NullItem type='dropdown' text='Infobox'>
												<DropdownList>
													<ModalItem type='dropdown' text='Add Infobox' ModalComponent={ InfoboxCreationForm } />
													<ModalItem type='dropdown' text='Edit Infobox' ModalComponent={ InfoboxModificationForm } />
													<ModalItem type='dropdown' text='Delete Infobox' ModalComponent={ InfoboxDeletionForm } />
												</DropdownList>
											</NullItem>
										</DropdownList>
									</NullItem>
								</>
							) : (<React.Fragment />)
						}
					</ul>
					<ul className="navbar-list navbar-list-right">
						<SearchBar />
						{
							globalState.isAuthenticated
							? (
								<NullItem text={ globalState.preferredName || 'Account' }>
									<DropdownList>
										<ModalItem type='dropdown' text='Change Password' ModalComponent={ ChangePasswordForm } />
										<OnClickItem type='dropdown' text='Sign Out' onClick={ async () => {
											await makeApiPostRequest('/auth/', 'sign out', {}, globalState.csrfTokens, () => { location.reload(); })
										} } />
									</DropdownList>
								</NullItem>
							) : (
								<>
									<ModalItem text='Sign up' ModalComponent={ SignUpForm } />
									<ModalItem text='Sign in' ModalComponent={ SignInForm } />
								</>
							)
						}
						{/* <HamburgerToggleButton */}
						{/* 	isOnFn={ state => globalDispatch({ type: SET_RIGHT_SIDEBAR, payload: state }) } */}
						{/* /> */}
					</ul>
				</div>
			</nav>
		</div>
	);
};
