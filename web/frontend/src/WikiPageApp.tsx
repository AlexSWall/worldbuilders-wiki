import React, { ReactElement, useContext } from 'react';

import { GlobalStateContext } from 'GlobalState';

import { Footer } from './Components/Footer';
import { NavigationBar } from './Components/NavigationBar';
import { Sidebar } from './Components/Sidebars/Sidebar';
import { WikiPageLoader } from './Components/WikiPageLoader';

export const WikiPageApp = (): ReactElement =>
{
	const globalState = useContext( GlobalStateContext );

	return (
		<div id="pageWrapper">
			<NavigationBar />
			<main>
				<div id="contentWrapper">
					<div id="content">
						{ <Sidebar sidebar={ globalState.leftSidebar }/> }
						<div id="mainPanelWrapper">
							<div className="mainPanel">
								<WikiPageLoader />
							</div>
						</div>
						{ <Sidebar sidebar={ globalState.rightSidebar }/> }
					</div>
				</div>
			</main>
			<Footer />
		</div>
	);
};
