import React, { ReactElement } from 'react';

interface Props
{
	title: string;
	html: string;
};

export const WikiPanel = ({ title, html }: Props): ReactElement =>
{
	return (
		<>
			<h1 className="wiki-header">{ title }</h1>
			<div dangerouslySetInnerHTML={ { __html: html } } />
		</>
	);
};
