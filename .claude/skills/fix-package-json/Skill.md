---
name: Fix Package JSON
description: Sort the `dependencies` and `devDependencies` blocks of package.json alphabetically so the project's package.json linter passes. Detects the lint command, reorders the entries only, and never changes versions, adds or removes packages, or touches the lockfile or any other field. Use whenever the user asks to fix or sort package.json, fix lint:pkg-json / npm-package-json-lint errors, or mentions a prefer-alphabetical-dependencies error.
---

# Fix Package JSON — sort dependencies

Portable: no script name or package manager is assumed. Both are detected in step 0.

## 0. Profile the project

- **Lint command**: look in the `scripts` of `package.json` for one that runs
  `npm run lint:pkg-json`. If there is none,
  use `npx wp-scripts lint-pkg-json` when `@wordpress/scripts` is installed. 
 If neither is available, skip the linter.
- **File style**: note the indentation (tabs or spaces) and the trailing newline of `package.json`.

## 1. Fix

1. Run the lint command and read the errors.
2. Sort the keys of `dependencies` and `devDependencies` alphabetically by package name, the way
   `npm install` writes them (scoped `@…` packages first).
3. Re-run the lint command to confirm the ordering errors are gone.

## 2. Rules

- Change only the order of entries inside `dependencies` and `devDependencies`.
- Never change a version, add or remove a package, or move one between the two blocks.
- Never touch any other field, the lockfile, or `node_modules`, and do not run an install —
  reordering does not need one.
- Keep the file's indentation and trailing newline exactly as they were.
- If the linter reports anything other than dependency ordering, or a package appears in both
  blocks, report it — do not fix it.

## 3. Verify and report

1. `package.json` is still valid JSON.
2. `git diff` shows only moved lines inside the two blocks.
3. Report which blocks were reordered, and list any remaining linter errors separately.
