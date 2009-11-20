<?php
/**
 * Copyright (c) 2009 Arne Blankerts <arne@blankerts.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Arne Blankerts nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Scanner
 * @author     Arne Blankerts <arne@blankerts.de>
 * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license    BSD License
 */

namespace TheSeer\Tools {

   /**
    * Recursive scanner for files on given filesystem path with the ability to filter
    * results based on include and exclude patterns
    *
    * @author     Arne Blankerts <arne@blankerts.de>
    * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
    */
   class DirectoryScanner {

      /**
       * List of filter for include shell patterns
       *
       * @var Array
       */
      protected $include = array();

      /**
       * List of filter for exclude shell patterns
       *
       * @var Array
       */
      protected $exclude = array();

      /**
       * Add a new pattern to the include array
       *
       * @param string $inc Pattern to add
       *
       * @return void
       */
      public function addInclude($inc) {
         $this->include[] = $inc;
      }

      /**
       * set the include pattern array
       *
       * @param Array $inc Array of include pattern strings
       *
       * @return void
       */
      public function setIncludes(array $inc = array()) {
         $this->includes = $inc;
      }

      /**
       * get array of current include patterns
       *
       * @return Array
       */
      public function getIncludes() {
         return $this->includes;
      }

      /**
       * Add a new pattern to the exclude array
       *
       * @param string $inc Pattern to add
       *
       * @return void
       */
      public function addExclude($exc) {
         $this->exclude[] = $exc;
      }

      /**
       * set the exclude pattern array
       *
       * @param Array $exc Array of exclude pattern strings
       *
       * @return void
       */
      public function setExcludes(array $exc = array()) {
         $this->excludes = $exc;
      }

      /**
       * get array of current exclude patterns
       *
       * @return Array
       */
      public function getExcludes() {
         return $this->excludes;
      }

      /**
       * get an array of splFileObjects from given path matching the
       * include/exclude patterns
       *
       * @param string $path Path to work on
       *
       * @return Array of splFileInfo Objects
       */
      public function getFiles($path) {
         $res = array();
         foreach($this->__invoke($path) as $entry) {
            $res[] = $entry;
         }
         return $res;
      }

      /**
       * Magic invoker method to use object in foreach-alike constructs as iterator,
       * returning splFileObjects matching the include/exclude patterns of given path
       *
       * @param string $path Path to work on
       *
       * @return Iterator
       */
      public function __invoke($path) {
         if (!file_exists($path)) {
            throw new DirectoryScannerException("Path '$path' does not exist.", ScannerException::NotFound);
         }
         $filter = new IncludeExcludeFilterIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path))
         );
         $filter->setInclude( count($this->include) ? $this->include : array('*'));
         $filter->setExclude($this->exclude);
         return $filter;
      }

   }

   /**
    * DirectoryScanner Exception class
    *
    * @author     Arne Blankerts <arne@blankerts.de>
    * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
    */
   class DirectoryScannerException extends \Exception {

      /**
       * Error constant for "notFound" condition
       *
       * @var integer
       */
      const NotFound = 1;
   }

}