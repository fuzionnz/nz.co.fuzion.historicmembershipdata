# nz.co.fuzion.historicmembershipdata

![Screenshot](/images/search.jpg)

Provide a new search form which allows you to search for members that were active on a specific date in the past. Eg Find all the members that were active at the start of this year.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl nz.co.fuzion.historicmembershipdata@https://github.com/fuzionnz/nz.co.fuzion.historicmembershipdata/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/nz.co.fuzion.historicmembershipdata.git
cv en historicmembershipdata
```

## Usage

- Install the ext and navigate to Search => Custom Searches
- Find the search form with the name `PastMembersearch (nz.co.fuzion.historicmembershipdata)`.
- Enter the date in the Active On field. Hit search.

