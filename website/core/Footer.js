/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

const React = require('react');

class Footer extends React.Component {
  docUrl(doc, language) {
    const baseUrl = this.props.config.baseUrl;
    const docsUrl = this.props.config.docsUrl;
    const docsPart = `${docsUrl ? `${docsUrl}/` : ''}`;
    const langPart = `${language ? `${language}/` : ''}`;
    return `${baseUrl}${docsPart}${langPart}${doc}`;
  }

  pageUrl(doc, language) {
    const baseUrl = this.props.config.baseUrl;
    return baseUrl + (language ? `${language}/` : '') + doc;
  }

  render() {
    return (
      <footer className="nav-footer" id="footer">
        <section className="sitemap">
          <a href={this.props.config.baseUrl} className="nav-home">
            {this.props.config.footerIcon && (
              <img
                src={this.props.config.baseUrl + this.props.config.footerIcon}
                alt={this.props.config.title}
                width="66"
                height="58"
              />
            )}
          </a>
          <div>
            <h5>Docs</h5>
            <a href={this.docUrl('getting-started', this.props.language)}>
              Install
            </a>
            <a href={this.docUrl('my-first-query', this.props.language)}>
              Getting started
            </a>
            <a href={this.docUrl('annotations_reference', this.props.language)}>
              Reference
            </a>
          </div>

          <div>
            <h5>More</h5>
            <a href="https://thecodingmachine.io" target="_blank">Blog</a>
            <a href="https://github.com/thecodingmachine/graphqlite" target="_blank">GitHub</a>
            <a className="github-button"
               href="https://github.com/thecodingmachine/graphqlite"
               data-icon="octicon-star"
               data-show-count="true"
               aria-label="Star thecodingmachine/graphqlite on GitHub">Star</a>
          </div>
        </section>

        <a
          href="https://thecodingmachine.io/open-source"
          target="_blank"
          className="fbOpenSource">
          <img
            src={`${this.props.config.baseUrl}img/tcm.png`}
            alt="Proudly brought to you by TheCodingMachine"
          />
        </a>
        <section className="copyright">{this.props.config.copyright}</section>
      </footer>
    );
  }
}

module.exports = Footer;
