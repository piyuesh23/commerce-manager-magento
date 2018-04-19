#!/bin/bash

# Creates a .tar.gz archive of this repository suitable for submission to Magento marketplace

dist_dir="."
dist_name="commerce-manager-magento"
dist_tag="2.1.0"

git archive --worktree-attributes --prefix=${dist_name}/ -o ${dist_dir}/${dist_name}_${dist_tag}.tar.gz ${dist_tag}

if [ ! -f ${dist_dir}/${dist_name}_${dist_tag}.tar.gz ]; then
    echo "Failed to create archive ${dist_dir}/${dist_name}_${dist_tag}.tar.gz"
    exit 1
fi

echo "Created ${dist_dir}/${dist_name}_${dist_tag}.tar.gz"
exit 0
