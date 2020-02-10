#!/usr/bin/env bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.35.2/install.sh | XDG_CONFIG_HOME=/usr/local bash \
&& . ${NVM_DIR}/nvm.sh \
&& IFS=',' read -r -a versions <<< "${INSTALL_NODEJS_VERSIONS}" \
&& for version in "${versions[@]}"; do nvm install $version; done \
&& nvm alias default ${versions[0]} \
