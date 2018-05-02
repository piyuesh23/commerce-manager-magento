#!/bin/bash
#
# Creates a .tar.gz archive of this repository suitable for submission to Magento marketplace

set -ex

git fetch --all -q
git fetch --tags -q

if [[ -n "`git status --porcelain`" ]]; then
  echo "ERROR: Please run from a clean checkout."
  exit 1
fi

current_dir=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
dist_dir=${current_dir}/dist
dist_name="commerce-manager-magento"

if [[ "${TRAVIS}" == "true" ]]; then
  git_tag=$(git rev-parse --short HEAD)
  dist_tag=${git_tag}-dev
  success_message="Successfully created *DEVELOPMENT* distribution "
else
  git_tag=$(git describe --exact-match --tags HEAD)
  dist_tag=${git_tag}
  success_message="Successfully created tagged release "
fi

mkdir -p ${dist_dir}
git archive --worktree-attributes --prefix=${dist_name}/ -o ${dist_dir}/${dist_name}_${dist_tag}.tar.gz ${git_tag}

if [ ! -f ${dist_dir}/${dist_name}_${dist_tag}.tar.gz ]; then
    echo "Failed to create archive ${dist_dir}/${dist_name}_${dist_tag}.tar.gz"
    exit 1
fi

echo "${success_message}"
echo "${dist_dir}/${dist_name}_${dist_tag}.tar.gz"
