FROM node:14.9
 
WORKDIR /reactui
 
COPY reactui/package*.json ./
 
RUN npm install
 
COPY ./reactui/ .
 
EXPOSE 8080
 
CMD [ "npm","run", "start" ]