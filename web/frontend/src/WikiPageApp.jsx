import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import Footer from './Components/Footer';
import Sidebar from './Components/Sidebar';
import WebpageLoader from './Components/WebpageLoader';

import WikiPanel from './Components/WikiPanel';
import EditWebpage from './Components/Special/Forms/EditWebpage'

export default function WikiPageApp({ authenticationData, flash })
{
	return (
		<div id="pageWrapper">
			<NavigationBar authenticationData={authenticationData} />
			<Flash flash={flash} />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div id="mainPanel">
								<WebpageLoader 
									urlBase='/w/'
									componentMapper={ (path) =>
										{
											if (path === 'Special:Edit_Wiki_Page')
												return EditWebpage;
											else
												return WikiPanel;
										}
									}
								/>
							</div>
						</div>
					</div>
				</div>
			</main>
			<Footer />
			<Sidebar />
		</div>
	);
}
