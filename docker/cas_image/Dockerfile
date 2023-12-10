FROM apereo/cas:6.6.2
EXPOSE 8080 8443
RUN keytool -genkey -alias cas -keyalg RSA -validity 999 \
	-keypass changeit -storepass changeit \
	-dname "CN=cas" \
    -keystore /etc/cas/thekeystore -ext san=dns:cas
WORKDIR /etc/cas
ADD cas.properties config/
ADD services/* services/
ADD attribute-repository.json .
ENV LANG=en_US.UTF-8 LANGUAGE=en_US:en LC_ALL=en_US.UTF-8
ENV JAVA_VERSION=jdk-11.0.17+8
ENV JAVA_HOME=/opt/java/openjdk
ENV PATH=/opt/java/openjdk/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/opt/java/openjdk/bin:.
WORKDIR /docker/cas/war
