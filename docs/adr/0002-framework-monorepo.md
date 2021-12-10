# 2. Framework Monorepo

Date: 2021-12-06

## Status

Accepted

## Context

The framework packages have a very similar, if not identical structure. Managing them in a monorepo will reduce the
workload when adjusting dependencies or modernising the general workflow.

The available monorepo tools do not play nicely with a project like the framework, not least because they require a
common version number for all packages.

## Decision

Until a production ready tool exists to mitigate such issues, the monorepo is managed using `git-subtree` and a couple
of scripts stored in the `build` directory. Their usage is described in the `README.md` file in that directory.

## Consequences

* Dependencies can be kept coherent within a branch
* The package structure is unified
* Tools can be created to propagate general changes to all packages
