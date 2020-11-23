import React, { Component } from 'react';

import NavBarLink from '../NavBarLink';

import Modal from '../../Modal';

class CreateWikiPageNavBarLink extends Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			promptIsOpen: false
		}

	}

	render()
	{
		return (
			<>
				<NavBarLink onClick={ () => this.setState({promptIsOpen: true}) } text="Add Page" active={false} />
				<Modal open={this.state.promptIsOpen} onClose={() => this.setState({promptIsOpen: false})}>
					Foo bar
				</Modal>
			</>
		);
	}
}

export default CreateWikiPageNavBarLink;
