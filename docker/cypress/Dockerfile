FROM cypress/included:13.2.0

ARG WWWGROUP
ARG WWWUSER

RUN groupadd -o -g ${WWWGROUP} sail
# do not log creating new user, otherwise there could be a lot of messages
RUN useradd -r --no-log-init -o -u ${WWWUSER} -g sail sail
RUN install -d -m 0755 -o sail -g sail /home/sail

# move test runner binary folder to the non-root's user home directory
# RUN mv /root/.cache /home/sail/.cache

# RUN chown -R ${WWWUSER}:${WWWGROUP} /home/sail/.cache

USER sail

# show user effective id and group - it should be non-zero
# meaning the current user "node" is not root
RUN id

# ENV CYPRESS_CACHE_FOLDER=/home/sail/.cache/Cypress
ENV CYPRESS_CACHE_FOLDER=/root/.cache/Cypress
