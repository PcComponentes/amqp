FROM rabbitmq:3.7.7-management-alpine
COPY rabbitmq_delayed_message_exchange-20171201-3.7.x.ez /opt/rabbitmq/plugins
RUN rabbitmq-plugins enable --offline rabbitmq_delayed_message_exchange